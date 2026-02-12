<?php

namespace App\Livewire\Pages\Invoice;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.web')]
#[Title('Detail E-Invoice')]
class InvoiceProsesDetailPage extends Component
{
    public string $nomor;

    public bool $showFakturModal = false;

    public ?string $selectedItemId = null;

    public ?string $selectedIdTagihan = null;

    public ?string $selectedNoFaktur = null;

    public function mount(string $nomor): void
    {
        $this->nomor = $nomor;
    }

    public function invoice(): ?object
    {
        return DB::connection('mysql')->selectOne('
            SELECT
                e_invoice.id,
                e_invoice.nomor,
                e_invoice.status,
                e_invoice.e_materai_qty,
                e_invoice.created_at,
                pegawai.nama_depan AS pegawai,
                kantor.nama_kantor AS kantor,
                COUNT(e_invoice_item.id) AS qty
            FROM
                erp_invoice.e_invoice
                JOIN erp_hr.pegawai ON (pegawai.id_pegawai = e_invoice.id_pegawai)
                JOIN erp.kantor ON (e_invoice.id_kantor = kantor.id_kantor)
                LEFT JOIN erp_invoice.e_invoice_item ON (e_invoice.id = e_invoice_item.id_e_invoice)
            WHERE e_invoice.nomor = ?
            GROUP BY e_invoice.id
            LIMIT 1
        ', [addslashes($this->nomor)]);
    }

    public function getStatusLabelProperty(): string
    {
        $invoice = $this->invoice();

        return match ($invoice?->status) {
            'draft' => 'Draft',
            'proses' => 'Proses',
            'close' => 'Selesai',
            'cancel' => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusColorProperty(): string
    {
        $invoice = $this->invoice();

        return match ($invoice?->status) {
            'draft' => 'zinc',
            'proses' => 'blue',
            'close' => 'green',
            'cancel' => 'red',
            default => 'zinc',
        };
    }

    public function items(): array
    {
        $nomor = addslashes($this->nomor);

        return DB::connection('mysql')->select("
            SELECT
                e_invoice_item.id,
                e_invoice_item.id_tagihan,
                e_invoice_item.status,
                tagihan.tanggal_tagihan AS tanggal,
                tagihan.nilai,
                pelanggan.nama AS pelanggan,
                kontrak.no_kontrak,
                tagihan.no_kwitansi,
                faktur.no_faktur,
                faktur.id_faktur
            FROM
                erp_invoice.e_invoice_item
                JOIN erp_invoice.e_invoice ON (e_invoice.id = e_invoice_item.id_e_invoice)
                JOIN erp_invoice.tagihan ON (e_invoice_item.id_tagihan = tagihan.id_tagihan)
                JOIN erp_pelanggan.kontrak ON (kontrak.id_kontrak = tagihan.id_kontrak)
                JOIN erp_pelanggan.pelanggan ON (pelanggan.id_pelanggan = kontrak.id_pelanggan)
                JOIN erp.kantor ON (kantor.id_kantor = kontrak.id_kantor)
                JOIN erp_pelanggan.lokasi_tipe ON (kontrak.id_lolaksi_tipe = lokasi_tipe.id_lokasi_tipe)
                LEFT JOIN erp_invoice.faktur ON (faktur.id_tagihan = tagihan.id_tagihan)
            WHERE
                e_invoice.nomor = '{$nomor}'
            ORDER BY
                tagihan.tanggal_tagihan ASC
        ");
    }

    public function getItemStatusLabelProperty(): array
    {
        return [
            'open' => 'Open',
            'input' => 'Input',
            'file' => 'File',
            'trouble' => 'Trouble',
        ];
    }

    public function openInputFaktur(string $itemId): void
    {
        $this->selectedItemId = $itemId;
        $this->selectedIdTagihan = null;
        $this->selectedNoFaktur = null;

        foreach ($this->items() as $item) {
            if ($item->id === $itemId) {
                $this->selectedIdTagihan = $item->id_tagihan;
                $this->selectedNoFaktur = $item->no_faktur;
                break;
            }
        }

        $this->showFakturModal = true;
    }

    #[On('close-faktur-modal')]
    public function closeFakturModal(): void
    {
        $this->showFakturModal = false;
        $this->selectedItemId = null;
        $this->selectedIdTagihan = null;
        $this->selectedNoFaktur = null;
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex h-full min-h-screen flex-col overflow-hidden">
            @if(!$this->invoice())
                <div class="flex flex-1 items-center justify-center">
                    <div class="text-center">
                        <flux:icon.document-magnifying-glass class="mx-auto size-12 text-zinc-400" />
                        <flux:heading size="lg" class="mt-4">Invoice Tidak Ditemukan</flux:heading>
                        <flux:text class="mt-2 text-zinc-500">Nomor invoice "{{ $this->nomor }}" tidak ditemukan.</flux:text>
                    </div>
                </div>
            @else
                {{-- Header --}}
                <div id="header" class="flex h-11 shrink-0 items-center justify-between px-2.5 ">
                    <div class="flex items-center gap-3">
                        <flux:heading size="lg"> E-Invoice {{ $this->invoice()->nomor ?? '-' }}</flux:heading>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-auto border-y border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black">
                    <flux:table container:class="h-full w-full">
                        <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60 dark:bg-zinc-950/60 dark:border-zinc-700/60">
                            <flux:table.column class="w-20 pl-2! shrink-0">Status</flux:table.column>
                            <flux:table.column class="w-20 shrink-0">Tanggal</flux:table.column>
                            <flux:table.column class="w-44">Pelanggan</flux:table.column>
                            <flux:table.column class="w-32">No Kontrak</flux:table.column>
                            <flux:table.column class="w-32">No Kwitansi</flux:table.column>
                            <flux:table.column class="w-28 shrink-0">Nilai</flux:table.column>
                            <flux:table.column class="w-32">No Faktur</flux:table.column>
                            <flux:table.column class="w-20 shrink-0">Action</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($this->items() as $index => $item)
                                <flux:table.row :key="$item->id">
                                    <flux:table.cell class="pl-2!">
                                        <flux:badge size="sm" :color="match($item->status) { 'open' => 'zinc', 'input' => 'blue', 'file' => 'green', 'trouble' => 'red', default => 'zinc' }">
                                            {{ ucfirst($item->status) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $item->pelanggan }}">{{ $item->pelanggan }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $item->no_kontrak }}">{{ $item->no_kontrak }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $item->no_kwitansi }}">{{ $item->no_kwitansi }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell class="tabular-nums">{{ number_format($item->nilai, 0, ',', '.') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $item->no_faktur ?? '-' }}">{{ $item->no_faktur ?? '-' }}</div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:dropdown>
                                            <flux:button size="xs" variant="ghost" icon="ellipsis-horizontal" />
                                            <flux:menu>
                                                @if($item->no_faktur)
                                                    <flux:menu.item icon="pencil-square" wire:click="openInputFaktur('{{ $item->id }}')">
                                                        Update Faktur
                                                    </flux:menu.item>
                                                @else
                                                    <flux:menu.item icon="plus" wire:click="openInputFaktur('{{ $item->id }}')">
                                                        Input Faktur
                                                    </flux:menu.item>
                                                @endif
                                                <flux:menu.item icon="arrow-down-tray" wire:click="$dispatch('download-faktur', { itemId: '{{ $item->id }}' })">
                                                    Download Faktur
                                                </flux:menu.item>
                                                <flux:menu.item icon="arrow-down-tray" wire:click="$dispatch('download-tagihan', { itemId: '{{ $item->id }}' })">
                                                    Download Tagihan
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                                        Tidak ada item pada invoice ini.
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>

                {{-- Footer --}}
                <div class="flex shrink-0 items-center justify-between p-2.5">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Total: {{ count($this->items()) }} item
                    </div>
                </div>
            @endif

            {{-- Input/Update Faktur Modal --}}
            <flux:modal wire:model="showFakturModal" variant="flyout" class="max-w-full [&>[data-flux-modal-content]]:h-full [&>[data-flux-modal-content]>*]:h-full" :dismissible="false" :closable="false">
                @if($this->showFakturModal)
                    <livewire:pages.invoice.faktur-form
                        :id-tagihan="$selectedIdTagihan"
                        :nomor="$this->invoice()->nomor"
                        :existing-no-faktur="$selectedNoFaktur"
                        :key="'faktur-form-' . $selectedItemId"
                    />
                @endif
            </flux:modal>
        </div>
        HTML;
    }
}
