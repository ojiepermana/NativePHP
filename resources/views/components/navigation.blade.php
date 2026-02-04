<div x-data="{ collapsed: window.Alpine ? Alpine.raw($root.closest('[x-data]')).__x.$data.collapsed : false }" x-effect="collapsed = $root.closest('[x-data]').__x.$data.collapsed">
    <flux:sidebar.nav x-show="!collapsed" x-cloak>
        <flux:sidebar.item icon="home" href="#" current>Home</flux:sidebar.item>
        <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
        <flux:sidebar.item icon="document-text" href="#">Documents</flux:sidebar.item>
        <flux:sidebar.item icon="calendar" href="#">Calendar</flux:sidebar.item>

        <flux:sidebar.group expandable icon="star" heading="Favorites" class="grid">
            <flux:sidebar.item href="#">Marketing site</flux:sidebar.item>
            <flux:sidebar.item href="#">Android app</flux:sidebar.item>
            <flux:sidebar.item href="#">Brand guidelines</flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>

    <!-- Collapsed Icons -->
    <div x-show="collapsed" x-cloak class="flex flex-col items-center py-2">
        <flux:tooltip content="Home" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.home variant="mini" />
            </a>
        </flux:tooltip>

        <flux:tooltip content="Inbox" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.inbox variant="mini" />
            </a>
        </flux:tooltip>

        <flux:tooltip content="Documents" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.document-text variant="mini" />
            </a>
        </flux:tooltip>

        <flux:tooltip content="Calendar" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.calendar variant="mini" />
            </a>
        </flux:tooltip>

        <!-- Custom dropdown untuk Favorites -->
        <div x-data="{ open: false }" class="relative my-1">
            <button @click="open = !open" @click.away="open = false" class="p-2.5 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center" title="Favorites">
                <flux:icon.star variant="mini" />
            </button>
            <div x-show="open" x-cloak x-transition class="absolute left-full top-0 ml-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Marketing site</a>
                <a href="#" class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Android app</a>
                <a href="#" class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Brand guidelines</a>
            </div>
        </div>
    </div>

    <flux:sidebar.spacer x-show="!collapsed" x-cloak />
    <div x-show="collapsed" x-cloak class="h-4"></div>

    <flux:sidebar.nav x-show="!collapsed" x-cloak>
        <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
        <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
    </flux:sidebar.nav>

    <!-- Collapsed Icons - Bottom -->
    <div x-show="collapsed" x-cloak class="flex flex-col items-center py-2">
        <flux:tooltip content="Settings" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.cog-6-tooth variant="mini" />
            </a>
        </flux:tooltip>

        <flux:tooltip content="Help" position="right">
            <a href="#" class="p-2.5 my-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center">
                <flux:icon.information-circle variant="mini" />
            </a>
        </flux:tooltip>
    </div>
</div>
