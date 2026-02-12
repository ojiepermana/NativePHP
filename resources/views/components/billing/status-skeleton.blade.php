<flux:skeleton.group animate="shimmer">
    <flux:table container:class="h-full w-full">
        <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60 dark:bg-zinc-950/60 dark:border-zinc-700/60">
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
            @foreach (range(1, $rows ?? 100) as $index)
                <flux:table.row>
                    <flux:table.cell class="pl-2!">
                        <flux:skeleton.line class="w-10" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-20" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-28" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="max-w-4xl">
                            <flux:skeleton.line class="w-full" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-40" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-28" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-32" />
                    </flux:table.cell>

                    <flux:table.cell class="pr-2! text-right">
                        <flux:skeleton.line class="w-32 ml-auto" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:skeleton.group>
