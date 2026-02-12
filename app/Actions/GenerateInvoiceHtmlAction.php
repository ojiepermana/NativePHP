<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\GCS;
use App\Helpers\IndonesiaHelper;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GenerateInvoiceHtmlAction
{
    public function __construct(
        private readonly ViewFactory $view
    ) {}

    /**
     * Generate invoice HTML string.
     */
    public function execute(string $idFaktur): string
    {
        $invoiceData = $this->getInvoiceData($idFaktur);

        return $this->view->make('invoice.print', $invoiceData)->render();
    }

    /**
     * Generate and save HTML to GCS.
     */
    public function saveToFile(string $idFaktur): string
    {
        $html = $this->execute($idFaktur);
        $path = $this->getInvoicePath($idFaktur);

        try {
            $this->logUploadAttempt($idFaktur, $path, $html);

            // Upload using GCS facade
            $uploaded = GCS::disk('gcs-generate')->uploadFile($path, $html);

            if (! $uploaded) {
                throw new RuntimeException("Failed to upload file: {$path}");
            }

            // Verify upload
            if (! GCS::disk('gcs-generate')->exists($path)) {
                throw new RuntimeException("File verification failed after upload: {$path}");
            }

            $this->logUploadSuccess($idFaktur, $path);

            return $path;
        } catch (\Exception $e) {
            $this->logUploadFailure($idFaktur, $path, $e);

            throw new RuntimeException(
                "Failed to save invoice HTML for {$idFaktur}: {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Get invoice data formatted for view.
     */
    private function getInvoiceData(string $idFaktur): array
    {
        $faktur = $this->getFaktur($idFaktur);
        $items = $this->getInvoiceItems($faktur->id_tagihan);
        $total = $this->calculateTotal($faktur);

        return [
            'id_faktur' => $idFaktur,
            'invoice_number' => $faktur->no_kwi,
            'va_number' => $faktur->van,
            'faktur_number' => $faktur->no_faktur,
            'invoice_date' => IndonesiaHelper::tanggal($faktur->tanggal),
            'due_date' => now()->addDays(30)->format('d F Y'),
            'customer_name' => $faktur->nama,
            'customer_address' => $faktur->alamat,
            'keterangan' => $faktur->keterangan,
            'items' => $items,
            'dpp' => $faktur->dpp,
            'ppn' => $faktur->ppn,
            'pph23' => $faktur->pph_potong,
            'total' => $total,
            'terbilang' => IndonesiaHelper::terbilang($total).' rupiah',
        ];
    }

    /**
     * Get faktur from database.
     */
    private function getFaktur(string $idFaktur): object
    {
        $faktur = DB::connection('mysql')
            ->table('erp_invoice.faktur')
            ->select('faktur.*', 'pelanggan.van')
            ->join('erp_pelanggan.pelanggan', 'faktur.id_pelanggan', '=', 'pelanggan.id_pelanggan')
            ->where('id_faktur', $idFaktur)
            ->first();

        if (! $faktur) {
            throw new ModelNotFoundException("Invoice with ID {$idFaktur} not found");
        }

        return $faktur;
    }

    /**
     * Get invoice items from database.
     */
    private function getInvoiceItems(string $idTagihan): array
    {
        return DB::connection('mysql')
            ->table('erp_pelanggan.kontrak_tagihan')
            ->join('erp_invoice.tagihan_item', 'kontrak_tagihan.id_kontrak_tagihan', '=', 'tagihan_item.id_kontrak_tagihan')
            ->join('erp_invoice.tagihan', 'tagihan_item.id_tagihan', '=', 'tagihan.id_tagihan')
            ->join('erp_pelanggan.lokasi', 'kontrak_tagihan.id_lokasi', '=', 'lokasi.id_lokasi')
            ->where('tagihan_item.id_tagihan', $idTagihan)
            ->select('lokasi.alamat_lengkap', 'kontrak_tagihan.kunjungan', 'kontrak_tagihan.nilai')
            ->get()
            ->map(fn ($item) => [
                'location' => $item->alamat_lengkap,
                'quantity' => $item->kunjungan,
                'nilai' => $item->nilai,
            ])
            ->toArray();
    }

    /**
     * Generate GCS path for invoice.
     */
    private function getInvoicePath(string $idFaktur): string
    {
        $faktur = $this->getFaktur($idFaktur);
        $date = Carbon::parse($faktur->tanggal);

        return sprintf(
            'invoice/%s/%s/%s/%s/invoice.html',
            $date->format('Y'),
            $date->format('m'),
            $date->format('d'),
            $idFaktur
        );
    }

    /**
     * Calculate invoice total.
     */
    private function calculateTotal(object $faktur): float
    {
        return ($faktur->dpp + $faktur->ppn) - $faktur->pph_potong;
    }

    /**
     * Log upload attempt.
     */
    private function logUploadAttempt(string $idFaktur, string $path, string $html): void
    {
        Log::info('Attempting to save invoice to GCS', [
            'id_faktur' => $idFaktur,
            'path' => $path,
            'bucket' => config('filesystems.disks.gcs-generate.bucket'),
            'html_size' => strlen($html),
        ]);
    }

    /**
     * Log upload success.
     */
    private function logUploadSuccess(string $idFaktur, string $path): void
    {
        Log::info('Invoice saved successfully to GCS', [
            'id_faktur' => $idFaktur,
            'path' => $path,
        ]);
    }

    /**
     * Log upload failure.
     */
    private function logUploadFailure(string $idFaktur, string $path, \Exception $e): void
    {
        Log::error('Failed to save invoice to GCS', [
            'id_faktur' => $idFaktur,
            'path' => $path,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
