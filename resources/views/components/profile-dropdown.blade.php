<flux:dropdown position="top" align="start">
    <button class="relative w-7 h-7 rounded-full overflow-hidden hover:opacity-80 transition-opacity">
        <img src="https://fluxui.dev/img/demo/user.png" alt="User profile" class="w-full h-full object-cover" />
    </button>
    <flux:menu>
        <flux:menu.radio.group>
            <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
            <flux:menu.radio>Truly Delta</flux:menu.radio>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
    </flux:menu>
</flux:dropdown>
