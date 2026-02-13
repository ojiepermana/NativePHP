<flux:dropdown position="top" align="start">
    <button class="relative w-6.5 h-6.5 rounded-full overflow-hidden hover:opacity-80 transition-opacity">
        <img src="https://etos.co.id/assets/moggy-faicon.png" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover" />
    </button>
    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.radio checked>{{ auth()->user()->name }}</flux:menu.radio>
            <flux:menu.radio>{{ auth()->user()->id }}</flux:menu.radio>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <flux:menu.item icon="arrow-right-start-on-rectangle" type="submit">Logout</flux:menu.item>
        </form>
    </flux:menu>
</flux:dropdown>
