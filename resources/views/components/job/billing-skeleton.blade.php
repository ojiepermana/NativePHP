<flux:skeleton.group animate="shimmer">
    <flux:table container:class="h-full w-full">
        <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60 dark:bg-zinc-950/60 dark:border-zinc-700/60">
            <flux:table.column class="w-10 pl-2!">#</flux:table.column>
            <flux:table.column class="w-16">Tanggal</flux:table.column>
            <flux:table.column class="w-36">Kantor</flux:table.column>
            <flux:table.column class="w-96">Lokasi</flux:table.column>
            <flux:table.column class="w-36 text-right pr-2!">Nilai</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach (range(1, $rows ?? 20) as $index)
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
                        <div class="space-y-2 max-w-4xl">
                            <flux:skeleton.line class="w-full" />
                            <flux:skeleton.line class="w-3/4" />
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="pr-2! text-right">
                        <flux:skeleton.line class="w-32 ml-auto" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:skeleton.group>
