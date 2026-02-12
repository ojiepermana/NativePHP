<?php

namespace App\Livewire\Pages\Billing;

use App\Domains\Billing\Status\BillingStatusService;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.web')]
#[Title('Status Tagihan')]
class BillingStatusPage extends Component
{
    public string $status;

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

        return BillingStatusService::getList($this->status, $request);
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
        return BillingStatusService::getOffices();
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

    public function showLastPage(): bool
    {
        return $this->totalPages() > 7 && $this->page < $this->totalPages() - 2;
    }

    public function getStatusLabelProperty(): string
    {
        return match ($this->status) {
            'belum' => 'Tagihan Belum Lengkap',
            'bermasalah' => 'Tagihan Bermasalah',
            'dibatalkan' => 'Tagihan Dibatalkan',
            'selesai' => 'Tagihan Selesai',
            'verifikasi' => 'Dokumen Verifikasi',
            'arsip' => 'Dokumen Arsip',
            'digital' => 'Dokumen Digital',
            'faktur' => 'Dokumen Faktur',
            default => 'Status Tagihan',
        };
    }

    public function getStatusColorProperty(): string
    {
        return match ($this->status) {
            'belum' => 'amber',
            'bermasalah' => 'red',
            'dibatalkan' => 'gray',
            'selesai' => 'green',
            'verifikasi' => 'blue',
            'arsip' => 'purple',
            'digital' => 'cyan',
            'faktur' => 'indigo',
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
                <div id="header" class="flex h-11 shrink-0 items-center justify-between p-2.5">
                    <div class="flex items-center gap-3">
                        <flux:heading size="lg">{{ $this->statusLabel }}</flux:heading>
                    </div>

                    <div class=" items-center gap-3 hidden lg:flex">
                        <flux:select wire:model.live="selectedOffice" placeholder="Semua Kantor" size="sm" class="min-w-40">
                            <option value="">Semua Kantor</option>
                            @foreach($this->offices() as $office)
                                <option value="{{ $office }}">{{ $office }}</option>
                            @endforeach
                        </flux:select>

                        <flux:input
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari No. Kontrak atau Pelanggan..."
                            size="sm"
                            class="min-w-64"
                            icon="magnifying-glass"
                        />
                    </div>
                </div>

                {{-- Table Content Area --}}
                <div class="flex-1 overflow-auto border-y border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black">
                    {{-- Loading Skeleton --}}
                    <div wire:loading>
                        <x-billing.status-skeleton :rows="100" />
                    </div>

                    {{-- Table with Data --}}
                    <div wire:loading.remove class="h-full">
                        <flux:table container:class="h-full w-full">
                            <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60  dark:bg-zinc-950/60 dark:border-zinc-700/60">
                                <flux:table.column class="w-10 pl-2! shrink-0">#</flux:table.column>
                                <flux:table.column class="w-24 shrink-0">Tanggal</flux:table.column>
                                <flux:table.column class="w-36 shrink-0">Kantor</flux:table.column>
                                <flux:table.column class="min-w-0 w-full">Pelanggan</flux:table.column>
                                <flux:table.column class="w-48 shrink-0">No. Kontrak</flux:table.column>
                                <flux:table.column class="w-36 shrink-0">Segmentasi</flux:table.column>
                                <flux:table.column class="w-48 shrink-0">Dokumen Belum</flux:table.column>
                                <flux:table.column class="w-36 shrink-0 text-right pr-2!">Nilai</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach($this->data() as $row)
                                <flux:table.row
                                    :key="$row->id_tagihan"
                                    class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800"
                                >
                                    <flux:table.cell class="tabular-nums pl-2!">
                                        {{ $row->realisasi ?? 0 }}/{{ $row->kunjungan ?? 0 }}
                                    </flux:table.cell>

                                    <flux:table.cell class="whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($row->tanggal_tagihan)->format('d/m/Y') }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                       {{ $row->kantor }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="max-w-4xl">
                                            <div class="font-medium text-zinc-900 dark:text-white truncate" title="{{ $row->pelanggan }}">
                                                {{ $row->pelanggan }}
                                            </div>
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="text-sm text-zinc-500 dark:text-zinc-400 truncate" title="{{ $row->no_kontrak }}">
                                            {{ $row->no_kontrak }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-300 truncate" title="{{ $row->segmentasi ?? '' }}">
                                            {{ $row->segmentasi ?? '-' }}
                                        </div>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        @if($row->dokumen_belum)
                                                {{ $row->dokumen_belum }}
                                        @else
                                            -
                                        @endif
                                    </flux:table.cell>

                                    <flux:table.cell class="text-right tabular-nums pr-2!">
                                        Rp {{ number_format($row->nilai, 0, ',', '.') }}
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

                    <div class="flex items-center gap-2">
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
        </div>
        HTML;
    }
}
