<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class DashboardPage extends Component
{
    #[Layout('components.layouts.web')]
    #[Title('Dashboard')]
    public function render()
    {
        return <<<'HTML'
        <div class="flex h-full min-h-screen flex-col">
            <div id="header" class="flex h-11 shrink-0 items-center justify-between p-2.5">
                <div class="flex items-center gap-3">
                    <flux:heading size="lg">Dashboard</flux:heading>
                </div>

                <div class="pr-2.5"></div>
            </div>

            <div class="relative flex-1 overflow-auto border-y border-neutral-300 bg-white dark:border-neutral-800 dark:bg-black">
                <div class="pointer-events-none absolute inset-x-0 -top-20 h-96 bg-gradient-to-r from-sky-500/25 via-emerald-400/25 to-orange-400/25 blur-3xl"></div>

                <div class="relative mx-auto max-w-6xl space-y-6 p-4 sm:p-6 lg:p-8">
                    <section class="overflow-hidden rounded-3xl border border-sky-200/70 bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-500 p-[1px] shadow-[0_25px_60px_-28px_rgba(14,165,233,0.85)] dark:border-sky-900/70">
                        <div class="rounded-[23px] bg-white/90 p-6 backdrop-blur-xl dark:bg-zinc-950/85 sm:p-8">
                            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
                                <div class="space-y-4">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-sky-300 bg-sky-100/70 px-3 py-1 text-xs font-semibold text-sky-800 dark:border-sky-700 dark:bg-sky-950/50 dark:text-sky-200">
                                        <flux:icon.sparkles class="size-4" />
                                        Focus Hari Ini
                                    </span>

                                    <h1 class="text-2xl font-bold leading-tight text-zinc-900 dark:text-zinc-50 sm:text-4xl">
                                        Kelola invoice lebih cepat,
                                        <span class="bg-gradient-to-r from-sky-600 via-cyan-600 to-emerald-600 bg-clip-text text-transparent dark:from-sky-300 dark:via-cyan-300 dark:to-emerald-300">lebih rapi, dan minim hambatan.</span>
                                    </h1>

                                    <p class="max-w-2xl text-sm text-zinc-600 dark:text-zinc-300 sm:text-base">
                                        Semua peran bekerja pada satu alur digital. Dari data pekerjaan sampai distribusi e-invoice,
                                        tiap proses bisa dipantau dan diselesaikan lebih terarah.
                                    </p>

                                    <div class="flex flex-wrap gap-2">
                                        <flux:badge color="emerald" size="sm">Automation</flux:badge>
                                        <flux:badge color="cyan" size="sm">Akuntabel</flux:badge>
                                        <flux:badge color="orange" size="sm">Paperless</flux:badge>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1">
                                    <div class="rounded-2xl border border-sky-200 bg-white/80 p-4 dark:border-sky-900 dark:bg-zinc-900/70">
                                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Peran Aktif</p>
                                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">4 Tim</p>
                                    </div>

                                    <div class="rounded-2xl border border-emerald-200 bg-white/80 p-4 dark:border-emerald-900 dark:bg-zinc-900/70">
                                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tahapan Proses</p>
                                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">6 Status</p>
                                    </div>

                                    <div class="rounded-2xl border border-orange-200 bg-white/80 p-4 dark:border-orange-900 dark:bg-zinc-900/70">
                                        <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Target</p>
                                        <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">100% Digital</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="space-y-3">
                        <div class="flex items-center justify-between">
                            <flux:heading size="lg">Peran & Tanggung Jawab</flux:heading>
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Kolaborasi antar divisi</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="group rounded-2xl border border-neutral-200 bg-white/85 p-4 transition hover:-translate-y-1 hover:border-sky-300 hover:shadow-lg dark:border-neutral-800 dark:bg-zinc-950/80 dark:hover:border-sky-800">
                                <div class="mb-3 inline-flex rounded-xl bg-sky-100 p-2 text-sky-600 dark:bg-sky-950/60 dark:text-sky-300">
                                    <flux:icon.briefcase class="size-5" />
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Staff Cabang</h3>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Mengontrol data pekerjaan dan memastikan data tagihan siap menjadi dokumen digital.</p>
                            </article>

                            <article class="group rounded-2xl border border-neutral-200 bg-white/85 p-4 transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg dark:border-neutral-800 dark:bg-zinc-950/80 dark:hover:border-cyan-800">
                                <div class="mb-3 inline-flex rounded-xl bg-cyan-100 p-2 text-cyan-600 dark:bg-cyan-950/60 dark:text-cyan-300">
                                    <flux:icon.shield-check class="size-5" />
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Supervisor</h3>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Memastikan syarat operasional lengkap dan setiap SPKO terhubung langsung ke tagihan.</p>
                            </article>

                            <article class="group rounded-2xl border border-neutral-200 bg-white/85 p-4 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg dark:border-neutral-800 dark:bg-zinc-950/80 dark:hover:border-emerald-800">
                                <div class="mb-3 inline-flex rounded-xl bg-emerald-100 p-2 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <flux:icon.megaphone class="size-5" />
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Sales AE/MS</h3>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Melengkapi syarat penagihan dari sisi marketing agar proses invoice tidak tertunda.</p>
                            </article>

                            <article class="group rounded-2xl border border-neutral-200 bg-white/85 p-4 transition hover:-translate-y-1 hover:border-orange-300 hover:shadow-lg dark:border-neutral-800 dark:bg-zinc-950/80 dark:hover:border-orange-800">
                                <div class="mb-3 inline-flex rounded-xl bg-orange-100 p-2 text-orange-600 dark:bg-orange-950/60 dark:text-orange-300">
                                    <flux:icon.banknotes class="size-5" />
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Finance Cabang</h3>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Memproses tagihan digital ke tahap faktur pajak dan e-materai dengan kontrol dokumen penuh.</p>
                            </article>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-neutral-200 bg-white/80 p-4 dark:border-neutral-800 dark:bg-zinc-950/75 sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <flux:heading size="base">Alur Singkat</flux:heading>
                            <flux:badge size="sm">End-to-End Flow</flux:badge>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-xl border border-sky-200 bg-white p-3 dark:border-sky-900/70 dark:bg-zinc-900/80">
                                <p class="inline-flex rounded-full bg-sky-500 px-2 py-0.5 text-xs font-semibold uppercase text-white">01</p>
                                <p class="mt-2 text-sm font-semibold text-sky-900 dark:text-sky-100">Input Data Pekerjaan</p>
                            </div>
                            <div class="rounded-xl border border-cyan-200 bg-white p-3 dark:border-cyan-900/70 dark:bg-zinc-900/80">
                                <p class="inline-flex rounded-full bg-cyan-500 px-2 py-0.5 text-xs font-semibold uppercase text-white">02</p>
                                <p class="mt-2 text-sm font-semibold text-cyan-900 dark:text-cyan-100">Validasi Syarat</p>
                            </div>
                            <div class="rounded-xl border border-emerald-200 bg-white p-3 dark:border-emerald-900/70 dark:bg-zinc-900/80">
                                <p class="inline-flex rounded-full bg-emerald-500 px-2 py-0.5 text-xs font-semibold uppercase text-white">03</p>
                                <p class="mt-2 text-sm font-semibold text-emerald-900 dark:text-emerald-100">Proses E-Invoice</p>
                            </div>
                            <div class="rounded-xl border border-orange-200 bg-white p-3 dark:border-orange-900/70 dark:bg-zinc-900/80">
                                <p class="inline-flex rounded-full bg-orange-500 px-2 py-0.5 text-xs font-semibold uppercase text-white">04</p>
                                <p class="mt-2 text-sm font-semibold text-orange-900 dark:text-orange-100">Distribusi & Monitoring</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        HTML;
    }
}
