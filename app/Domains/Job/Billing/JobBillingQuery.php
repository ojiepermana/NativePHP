<?php

namespace App\Domains\Job\Billing;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobBillingQuery
{
    public static function query(string $status, JobBillingFilter $filter): array
    {
        $sql = self::buildSql($status, $filter);

        // Async SQL logging (local only)
        if (app()->environment(['local', 'development'])) {
            defer(fn () => Log::channel('daily')->info('JobBillingQuery', [
                'status' => $status,
                'sql' => $sql,
            ]));
        }

        return DB::connection('mysql')->select($sql);
    }

    public static function count(string $status, JobBillingFilter $filter): int
    {
        $sql = self::buildCountSql($status, $filter);

        if (app()->environment(['local', 'development'])) {
            defer(fn () => Log::channel('daily')->info('JobBillingQuery::count', [
                'status' => $status,
                'sql' => $sql,
            ]));
        }

        $result = DB::connection('mysql')->select($sql);

        return $result[0]->total ?? 0;
    }

    private static function buildSql(string $status, JobBillingFilter $filter): string
    {
        $statusCondition = match ($status) {
            'belum' => "kontrak_tagihan.status = 'belum'",
            'lengkap' => "kontrak_tagihan.status = 'lengkap'",
            'selesai' => "kontrak_tagihan.status = 'selesai'",
            default => "kontrak_tagihan.status = '{$status}'"
        };

        $whereClause = $filter->toSqlFilter();
        $perPage = $filter->getPerPage();
        $offset = $filter->getOffset();

        return "
            SELECT
                kontrak_tagihan.id_kontrak_tagihan,
                lokasi.id_kantor,
                kontrak_tagihan.tanggal_tagihan,
                kontrak_tagihan.lastworked_at,
                kontrak_tagihan.realisasi,
                kontrak_tagihan.kunjungan,
                kontrak.no_kontrak,
                kantor.nama_kantor AS kantor,
                pelanggan.nama AS pelanggan,
                lokasi.alamat_lengkap AS alamat,
                lokasi_tipe.nama AS segmentasi,
                GROUP_CONCAT(master_syarat_tagihan.nama SEPARATOR ', ') AS dokumen_belum,
                kontrak_tagihan.nilai
            FROM erp_pelanggan.kontrak_tagihan
            JOIN erp_pelanggan.kontrak ON kontrak.id_kontrak = kontrak_tagihan.id_kontrak
            JOIN erp_pelanggan.lokasi ON lokasi.id_lokasi = kontrak_tagihan.id_lokasi
            JOIN erp.kantor ON lokasi.id_kantor = kantor.id_kantor
            JOIN erp_pelanggan.pelanggan ON pelanggan.id_pelanggan = lokasi.id_pelanggan
            JOIN erp_pelanggan.lokasi_tipe ON (lokasi_tipe.id_lokasi_tipe = lokasi.id_lokasi_tipe)
            LEFT JOIN erp_pelanggan.kontrak_tagihan_document ON kontrak_tagihan_document.id_kontrak_tagihan = kontrak_tagihan.id_kontrak_tagihan
                AND kontrak_tagihan_document.id_document IS NULL
            LEFT JOIN erp_pelanggan.master_syarat_tagihan ON master_syarat_tagihan.id_master_syarat_tagihan = kontrak_tagihan_document.id_master_syarat_tagihan
                AND master_syarat_tagihan.tipe = 'tagihan'
            WHERE {$statusCondition}
            AND YEAR(kontrak_tagihan.tanggal_tagihan) > 2025
            AND kontrak_tagihan.kunjungan > 0
            AND kontrak_tagihan.tanggal_tagihan <= LAST_DAY(NOW())
            AND kontrak_tagihan.deleted_at IS NULL
            {$whereClause}
            GROUP BY
                kontrak_tagihan.id_kontrak_tagihan,
                lokasi.id_kantor,
                kontrak_tagihan.tanggal_tagihan,
                kontrak_tagihan.lastworked_at,
                kontrak_tagihan.realisasi,
                kontrak_tagihan.kunjungan,
                kontrak.no_kontrak,
                kantor.nama_kantor,
                pelanggan.nama,
                lokasi.alamat_lengkap,
                lokasi_tipe.nama,
                kontrak_tagihan.nilai
            ORDER BY kontrak_tagihan.tanggal_tagihan ASC
            LIMIT {$perPage} OFFSET {$offset}
        ";
    }

    private static function buildCountSql(string $status, JobBillingFilter $filter): string
    {
        $statusCondition = match ($status) {
            'belum' => "kontrak_tagihan.status = 'belum'",
            'lengkap' => "kontrak_tagihan.status = 'lengkap'",
            'selesai' => "kontrak_tagihan.status = 'selesai'",
            default => "kontrak_tagihan.status = '{$status}'"
        };

        $whereClause = $filter->toSqlFilter();

        return "
            SELECT COUNT(DISTINCT kontrak_tagihan.id_kontrak_tagihan) as total
            FROM erp_pelanggan.kontrak_tagihan
            JOIN erp_pelanggan.kontrak ON kontrak.id_kontrak = kontrak_tagihan.id_kontrak
            JOIN erp_pelanggan.lokasi ON lokasi.id_lokasi = kontrak_tagihan.id_lokasi
            JOIN erp.kantor ON lokasi.id_kantor = kantor.id_kantor
            JOIN erp_pelanggan.pelanggan ON pelanggan.id_pelanggan = lokasi.id_pelanggan
            LEFT JOIN erp_pelanggan.kontrak_tagihan_document ON kontrak_tagihan_document.id_kontrak_tagihan = kontrak_tagihan.id_kontrak_tagihan
                AND kontrak_tagihan_document.id_document IS NULL
            LEFT JOIN erp_pelanggan.master_syarat_tagihan ON master_syarat_tagihan.id_master_syarat_tagihan = kontrak_tagihan_document.id_master_syarat_tagihan
                AND master_syarat_tagihan.tipe = 'tagihan'
            WHERE {$statusCondition}
            AND YEAR(kontrak_tagihan.tanggal_tagihan) > 2025
            AND kontrak_tagihan.kunjungan > 0
            AND kontrak_tagihan.tanggal_tagihan <= LAST_DAY(NOW())
            AND kontrak_tagihan.deleted_at IS NULL
            {$whereClause}
        ";
    }
}
