<?php

namespace App\Livewire\Pages\Invoice;

use App\Domains\Invoice\InvoiceProses\InvoiceProsesService;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.web')]
#[Title('E-Invoice')]
class InvoiceProsesStatusPage extends Component
{
    public string $status;

    public bool $showCreateModal = false;

    #[Url(as: 'office')]
    public ?string $selectedOffice = null;

    #[Url(as: 'search')]
    public ?string $search = null;

    #[Url(as: 'page')]
    public int $page = 1;

    public function mount(string $status, Request $request): void
    {
        $this->status = $status;
        $this->selectedOffice = $request->input('office');
        $this->search = $request->input('search');
        $this->page = max(1, (int) $request->input('page', 1));

        // Auto-open modal if no data and status is proses
        if ($this->status === 'proses' && $this->total() === 0 && ! $this->search && ! $this->selectedOffice) {
            $this->showCreateModal = true;
        }
    }

    public function updatedSelectedOffice(): void
    {
        $this->page = 1;
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages()) {
            $this->page++;
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function result(): array
    {
        $request = request()->duplicate();
        $request->merge([
            'office' => $this->selectedOffice,
            'search' => $this->search,
            'page' => $this->page,
        ]);

        return InvoiceProsesService::getList($this->status, $request);
    }

    public function data(): array
    {
        return $this->result()['data'];
    }

    public function total(): int
    {
        return $this->result()['total'];
    }

    public function totalPages(): int
    {
        return $this->result()['total_pages'];
    }

    public function offices(): array
    {
        return InvoiceProsesService::getOffices();
    }

    public function paginationPages(): array
    {
        if ($this->totalPages() <= 7) {
            return range(1, $this->totalPages());
        }

        $start = max(1, $this->page - 1);
        $end = min($this->totalPages(), $this->page + 1);

        return range($start, $end);
    }

    public function showFirstPage(): bool
    {
        return $this->totalPages() > 7 && $this->page > 3;
    }

    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    #[On('close-create-modal')]
    public function handleCloseModal(): void
    {
        $this->showCreateModal = false;
    }

    public function viewDetail(string $nomor): void
    {
        $this->redirect(route('invoice.proses.detail', ['nomor' => $nomor]), navigate: true);
    }

    public function showLastPage(): bool
    {
        return $this->totalPages() > 7 && $this->page < $this->totalPages() - 2;
    }

    public function getStatusLabelProperty(): string
    {
        return match ($this->status) {
            'draft' => 'E-Invoice Draft',
            'proses' => 'E-Invoice Proses',
            'selesai' => 'E-Invoice Selesai',
            'laporan' => 'E-Invoice Laporan',
            default => 'E-Invoice',
        };
    }

    public function getStatusColorProperty(): string
    {
        return match ($this->status) {
            'draft' => 'zinc',
            'proses' => 'blue',
            'selesai' => 'green',
            'laporan' => 'purple',
            default => 'zinc',
        };
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex h-full min-h-screen flex-col overflow-hidden">
            {{-- Empty State - Full Page --}}
            @if($this->total() === 0 && !$this->search && !$this->selectedOffice)
                <x-empty-state
                    title="Tidak Ada Data"
                    :message="'Tidak ada data ' . strtolower($this->statusLabel) . ' yang tersedia'"
                />

            @elseif($this->total() === 0)
                <x-empty-state
                    title="Tidak Ada Data Ditemukan"
                    message="Tidak ada data yang cocok dengan filter atau pencarian Anda"
                    :showReset="true"
                    resetAction="$set('search', null); $set('selectedOffice', null); $set('page', 1)"
                />
            @else
                {{-- Header --}}
                <div id="header" class="flex h-11 shrink-0 items-center justify-between px-2.5">
                    <div class="flex items-center gap-3">
                        <flux:heading size="lg">{{ $this->statusLabel }}</flux:heading>
                    </div>

                    <div class="items-center gap-3 hidden lg:flex">


                        <flux:select wire:model.live="selectedOffice" placeholder="Semua Kantor" size="sm" class="min-w-40">
                            <option value="">Semua Kantor</option>
                            @foreach($this->offices() as $office)
                                <option value="{{ $office }}">{{ $office }}</option>
                            @endforeach
                        </flux:select>

                        @if($this->selectedOffice && $this->total() > 0)
                            <flux:button wire:click="openCreateModal" size="sm" variant="primary">
                                E-Invoice
                            </flux:button>
                        @endif
                    </div>
                </div>

                {{-- Table Content Area --}}
                <div class="flex-1 overflow-auto border-y border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black">
                    {{-- Loading Skeleton --}}
                    <div wire:loading>
                        <x-invoice.proses-skeleton :rows="20" />
                    </div>

                    {{-- Table with Data --}}
                    <div wire:loading.remove class="h-full">
                        <flux:table container:class="h-full w-full">
                            <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60 dark:bg-zinc-950/60 dark:border-zinc-700/60">
                                <flux:table.column class="w-20 pl-2! shrink-0">Tanggal</flux:table.column>
                                <flux:table.column class="w-40 shrink-0">Pegawai</flux:table.column>
                                <flux:table.column class="w-40 shrink-0">Kantor</flux:table.column>
                                <flux:table.column class="w-48">Nomor</flux:table.column>
                                <flux:table.column class="w-24 shrink-0">E-Materai</flux:table.column>
                                <flux:table.column class="w-20 shrink-0">Qty</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach($this->data() as $row)
                                <flux:table.row
                                    :key="$row->id"
                                    wire:click="viewDetail('{{ $row->nomor }}')"
                                    class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800"
                                >
                                    <flux:table.cell class="whitespace-nowrap pl-2!">
                                        {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->pegawai }}">
                                            {{ $row->pegawai }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->kantor }}">
                                            {{ $row->kantor }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="truncate" title="{{ $row->nomor ?? '-' }}">
                                            {{ $row->nomor ?? '-' }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell class="tabular-nums">
                                        {{ $row->e_materai_qty ?? 0 }}
                                    </flux:table.cell>

                                    <flux:table.cell class="tabular-nums">
                                        {{ $row->qty ?? 0 }}
                                    </flux:table.cell>
                                </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="flex shrink-0 items-center justify-between p-2.5">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Halaman {{ $this->page }} dari {{ $this->totalPages() }}
                        <span class="ml-2">(Total: {{ number_format($this->total()) }} data)</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <flux:button
                            wire:click="previousPage"
                            :disabled="$this->page <= 1"
                            size="xs"
                            variant="ghost"
                        >
                            Sebelumnya
                        </flux:button>

                        @if($this->showFirstPage())
                            <flux:button wire:click="goToPage(1)" size="xs" variant="ghost">1</flux:button>
                            <span class="text-gray-400">...</span>
                        @endif

                        @foreach($this->paginationPages() as $pageNum)
                            <flux:button
                                wire:click="goToPage({{ $pageNum }})"
                                size="xs"
                                variant="{{ $this->page === $pageNum ? 'primary' : 'ghost' }}"
                            >
                                {{ $pageNum }}
                            </flux:button>
                        @endforeach

                        @if($this->showLastPage())
                            <span class="text-gray-400">...</span>
                            <flux:button wire:click="goToPage({{ $this->totalPages() }})" size="xs" variant="ghost">
                                {{ $this->totalPages() }}
                            </flux:button>
                        @endif

                        <flux:button
                            wire:click="nextPage"
                            :disabled="$this->page >= $this->totalPages()"
                            size="xs"
                            variant="ghost"
                        >
                            Berikutnya
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- Create Invoice Modal --}}
            <flux:modal wire:model="showCreateModal" variant="flyout"  :dismissible="false" :closable="false">
                <livewire:pages.invoice.create-invoice-list :office="$selectedOffice" />
            </flux:modal>
        </div>
        HTML;
    }
}
