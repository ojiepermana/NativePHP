<?php

namespace App\Livewire\Pages\Invoice;

use App\Domains\Billing\Status\BillingStatusFilter;
use App\Domains\Billing\Status\BillingStatusQuery;
use App\Domains\Invoice\InvoiceProses\InvoiceProsesService;
use Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateInvoiceList extends Component
{
    public ?string $selectedOffice = null;

    public bool $lockedOffice = false;

    public array $selectedItems = [];

    public bool $selectAll = false;

    public function mount(?string $office = null): void
    {
        if ($office) {
            $this->selectedOffice = $office;
            $this->lockedOffice = true;
        }
    }

    public function offices(): array
    {
        return InvoiceProsesService::getOffices();
    }

    public function updatedSelectedOffice(): void
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedItems = collect($this->getData())->pluck('id_tagihan')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function getData(): array
    {
        $filter = new BillingStatusFilter(
            office: $this->selectedOffice,
            search: null,
            page: 1
        );

        $data = BillingStatusQuery::query('belum', $filter);

        return array_slice($data, 0, 100);
    }

    public function closeModal(): void
    {
        $this->dispatch('close-create-modal');
    }

    public function save(): void
    {
        if (empty($this->selectedItems)) {
            return;
        }

        $user = auth()->user();
        $idKantor = DB::connection('mysql')
            ->table('erp.kantor')
            ->where('nama_kantor', $this->selectedOffice)
            ->value('id_kantor');

        try {
            DB::connection('mysql')->beginTransaction();

            // 1. Create e_invoice
            $eInvoiceId = (string) Str::uuid();

            DB::connection('mysql')->table('erp_invoice.e_invoice')->insert([
                'id' => $eInvoiceId,
                'id_pegawai' => $user->id,
                'id_kantor' => $idKantor,
                'status' => 'proses',
                'created_at' => now(),
            ]);

            // 2. Insert e_invoice_item for each selected tagihan
            $items = collect($this->selectedItems)->map(fn (string $idTagihan) => [
                'id' => (string) Str::uuid(),
                'id_e_invoice' => $eInvoiceId,
                'id_tagihan' => $idTagihan,
                'status' => 'open',
                'created_at' => now(),
            ])->toArray();

            DB::connection('mysql')->table('erp_invoice.e_invoice_item')->insert($items);

            // 3. Update status tagihan menjadi 'e-invoice'
            DB::connection('mysql')->table('erp_invoice.tagihan')
                ->whereIn('id_tagihan', $this->selectedItems)
                ->update(['status' => 'e-invoice']);

            DB::connection('mysql')->commit();

            // 4. Close modal and redirect
            $this->dispatch('close-create-modal');

            $this->redirect('/invoice/proses/proses', navigate: true);
        } catch (\Exception $e) {
            DB::connection('mysql')->rollBack();

            Flux::toast(
                variant: 'danger',
                text: 'Gagal membuat invoice: '.$e->getMessage(),
            );
        }
    }

    public function render()
    {
        return <<<'HTML'
        <form wire:submit="save" class="flex flex-col h-screen overflow-hidden -m-6 min-w-5xl">
            <div id="header" class="flex items-center justify-between p-2 shrink-0">
                <flux:heading size="lg">Form E-Invoice</flux:heading>
                <div>
                    @if($lockedOffice)
                        <flux:input value="{{ $selectedOffice }}" size="sm" class="min-w-48" disabled />
                    @else
                        <flux:select wire:model.live="selectedOffice" placeholder="Pilih Kantor" size="sm" class="min-w-48">
                        @foreach($this->offices() as $office)
                            <option value="{{ $office }}">{{ $office }}</option>
                        @endforeach
                        </flux:select>
                    @endif
                </div>
            </div>

            <div id="content" class="flex-1 overflow-auto min-h-0">
                @if($selectedOffice)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column class="w-12 shrink-0">
                                <flux:checkbox wire:model.live="selectAll" />
                            </flux:table.column>
                            <flux:table.column class="w-28 shrink-0">Tanggal</flux:table.column>
                            <flux:table.column class="w-40 shrink-0">Kantor</flux:table.column>
                            <flux:table.column class="w-48">Pelanggan</flux:table.column>
                            <flux:table.column class="w-40">No. Kontrak</flux:table.column>
                            <flux:table.column class="w-32">Segmentasi</flux:table.column>
                            <flux:table.column class="w-32 text-right">Nilai</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($this->getData() as $row)
                                <flux:table.row :key="$row->id_tagihan">
                                    <flux:table.cell>
                                        <flux:checkbox wire:model.live="selectedItems" value="{{ $row->id_tagihan }}" />
                                    </flux:table.cell>

                                    <flux:table.cell class="whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($row->tanggal_tagihan)->format('d/m/Y') }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->kantor }}">
                                            {{ $row->kantor }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->pelanggan }}">
                                            {{ $row->pelanggan }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->no_kontrak }}">
                                            {{ $row->no_kontrak }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->segmentasi }}">
                                            {{ $row->segmentasi }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell class="text-right tabular-nums">
                                        {{ number_format($row->nilai, 0, ',', '.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="7" class="text-center text-gray-500">
                                        Tidak ada data digital untuk kantor ini
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @else
                    <div class="flex items-center justify-center h-full text-gray-500">
                        Pilih kantor untuk menampilkan data
                    </div>
                @endif
            </div>

            <div id="footer" class="flex gap-2 justify-end px-2 py-4 shrink-0">
                <div class="mr-auto text-sm text-gray-600">
                    @if(count($selectedItems) > 0)
                        {{ count($selectedItems) }} item dipilih
                    @endif
                </div>
                <flux:button wire:click="closeModal" size="sm" variant="danger">Batal</flux:button>
                <flux:button type="submit" size="sm" variant="primary" :disabled="count($selectedItems) === 0">
                   Proses Invoice
                </flux:button>
            </div>
        </form>
        HTML;
    }
}
