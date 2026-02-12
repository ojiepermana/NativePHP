<flux:skeleton.group animate="shimmer">
    <flux:table container:class="h-full w-full">
        <flux:table.columns sticky class="bg-white/80 backdrop-blur-4xl border-b border-zinc-200/60 dark:bg-zinc-950/60 dark:border-zinc-700/60">
            <flux:table.column class="w-20 pl-2!">Tanggal</flux:table.column>
            <flux:table.column class="w-40">Pegawai</flux:table.column>
            <flux:table.column class="w-40">Kantor</flux:table.column>
            <flux:table.column class="w-32">ID</flux:table.column>
            <flux:table.column class="w-48">Nomor</flux:table.column>
            <flux:table.column class="w-24">Status</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach (range(1, $rows ?? 20) as $index)
                <flux:table.row>
                    <flux:table.cell class="pl-2!">
                        <flux:skeleton.line class="w-20" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-32" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-32" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-20" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-40" />
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:skeleton.line class="w-16" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:skeleton.group>
