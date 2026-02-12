<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://etos.co.id/assets/moggy-faicon.png">
    <title>{{ $title ?? 'Desktop' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])
    @fluxAppearance
    <script>
        if (!window.localStorage.getItem('flux.appearance')) {
            window.Flux?.applyAppearance('light');
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                collapsed: window.localStorage.getItem('sidebar.collapsed') === 'true',
                toggle() {
                    this.collapsed = !this.collapsed;
                    window.localStorage.setItem('sidebar.collapsed', this.collapsed);
                },
            });
        });
    </script>
</head>
<body class="flex justify-center h-screen overflow-hidden">
    <div class="relative flex min-w-0 flex-auto flex-row overflow-hidden  backdrop-blur-2xl bg-white/55 dark:bg-black/40  h-full rounded-2xl">
         <!-- Drag area at the top of main -->
    <div class="app-drag ml-18 absolute h-5 -mt-2.5 min-w-0 rounded-t-2xl w-full cursor-grabbing">&nbsp;</div>

     <div class="flex items-stretch overflow-hidden flex-1 min-h-0  bg-white/90 dark:bg-black/60 rounded-2xl py-2 pl-2">
             <div id="sidebar" x-data :class="$store.sidebar.collapsed ? 'w-16' : 'w-70'" class="shrink-0 sticky top-0 shad-xs flex flex-col h-full border bg-gradient-to-b from-blue-50/70 via-white/85 to-green-50/90 rounded-2xl border-neutral-300 dark:border-neutral-800 dark:from-blue-950/40 dark:via-black/50 dark:to-green-950/60 transition-all duration-300">
                {{-- border-b  border-neutral-300 dark:border-neutral-800  --}}
                <div id="header" class="w-full h-10 flex items-center justify-between px-4 shrink-0 ">
                    @if(request()->hasHeader('X-NativePHP-Secret'))
                    <div x-show="!$store.sidebar.collapsed" x-cloak>
                        <x-mac-window-controls />
                    </div>
                    @endif

                    <div x-show="!$store.sidebar.collapsed" x-cloak>
                        <x-brand href="/" logo="https://etos.co.id/assets/moggy-faicon.png" name="E-Invoice" />

                    </div>

                    <div class="flex items-center gap-1" :class="$store.sidebar.collapsed ? 'mx-auto' : ''">
                        <a @click="$store.sidebar.toggle()" class="cursor-pointer p-1" aria-label="Toggle sidebar">
                            <div x-show="!$store.sidebar.collapsed">
                                <x-sidebar-collapse-icon :collapsed="false" />
                            </div>
                            <div x-show="$store.sidebar.collapsed">
                                <x-sidebar-collapse-icon :collapsed="true" />
                            </div>
                        </a>
                        <a class="cursor-pointer" x-show="!$store.sidebar.collapsed" x-cloak x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" size="sm" square inset="top bottom" aria-label="Toggle dark mode">
                            <flux:icon.sparkles x-show="!$flux.dark" variant="mini" />
                            <flux:icon.sun x-show="$flux.dark" x-cloak  variant="mini"/>
                        </a>
                    </div>
                </div>
                <div id="nav" class="flex-1 w-full p-3 overflow-y-auto overflow-x-visible min-h-0">
                    <x-navigation />
                </div>
                {{-- border-t  border-neutral-300 dark:border-neutral-800  --}}
                <div id="footer" class="p-2 w-full h-10  flex items-center justify-between  shrink-0">
                    <div :class="$store.sidebar.collapsed ? 'mx-auto' : ''">
                        <x-profile-dropdown />
                    </div>
                    <div x-show="!$store.sidebar.collapsed" x-cloak class="flex gap-1 -mt-1">
                        <a href="#" class="p-2 hover:text-accent dark:hover:text-accent text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center transition-colors">
                            <flux:icon.cog-6-tooth class="size-6!" />
                        </a>
                        <a href="#" x-show="!$store.sidebar.collapsed" x-cloak class="p-2 hover:text-accent dark:hover:text-accent text-zinc-700 dark:text-zinc-300 inline-flex items-center justify-center transition-colors">
                            <flux:icon.information-circle class="size-6!" />
                        </a>
                    </div>
                </div>
            </div>
                <div id="content" class="flex-1 overflow-hidden  h-full min-h-0">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>
</html>

{{-- rounded-xl shadow-xl --}}
{{-- border border-neutral-200 dark:border-neutral-800  --}}
