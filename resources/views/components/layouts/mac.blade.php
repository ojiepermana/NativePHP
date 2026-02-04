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
    </script>
</head>
<body class="flex justify-center h-screen overflow-hidden">
    <div class="relative flex min-w-0 flex-auto flex-row overflow-hidden  backdrop-blur-3xl bg-white/85 dark:bg-black/80  h-full rounded-2xl">
         <!-- Drag area at the top of main -->
    <div class="app-drag ml-18 absolute h-5 -mt-2.5 min-w-0 rounded-t-3xl w-full">&nbsp;</div>

     <div class="flex items-stretch overflow-hidden flex-1 min-h-0  bg-white/85 dark:bg-black/50 rounded-2xl py-2 pl-2">
             <div id="sidebar" x-data="{ collapsed: false }" :class="collapsed ? 'w-16' : 'w-72'" class="shrink-0 sticky top-0 shad-xs flex flex-col items-start h-full border bg-white/85 rounded-2xl border-neutral-300 dark:border-neutral-800 dark:bg-black/50 transition-all duration-300">
                {{-- border-b  border-neutral-300 dark:border-neutral-800  --}}
                <div id="header" class="w-full h-10 flex items-center justify-between px-4">
                    <div x-show="!collapsed" x-cloak>
                        <x-mac-window-controls />
                    </div>
                    <div class="flex items-center gap-1" :class="collapsed ? 'mx-auto' : ''">
                        <a @click="collapsed = !collapsed" class="cursor-pointer text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-zinc-100" aria-label="Toggle sidebar">
                            <flux:icon.chevron-left x-show="!collapsed" variant="mini" />
                            <flux:icon.chevron-right x-show="collapsed" variant="mini" />
                        </a>
                        <a class="cursor-pointer" x-show="!collapsed" x-cloak x-data x-on:click="$flux.dark = ! $flux.dark" variant="subtle" size="sm" square inset="top bottom" aria-label="Toggle dark mode">
                            <flux:icon.sparkles x-show="!$flux.dark" variant="mini" />
                            <flux:icon.sun x-show="$flux.dark" x-cloak  variant="mini"/>
                        </a>
                    </div>
                </div>
                <div id="nav" class="flex-1 w-full p-3">
                    <x-navigation />
                </div>
                {{-- border-t  border-neutral-300 dark:border-neutral-800  --}}
                <div id="footer" class="w-full h-10 px-3" x-show="!collapsed">
                    <flux:dropdown position="top" align="start">
                        <flux:sidebar.profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />

                        <flux:menu>
                            <flux:menu.radio.group>
                                <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                                <flux:menu.radio>Truly Delta</flux:menu.radio>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.item icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
                <div id="content" class="flex-1 overflow-auto  h-full min-h-0  rounded-r-3xl dark:bg-black/50">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>
</html>

{{-- rounded-xl shadow-xl --}}
{{-- border border-neutral-200 dark:border-neutral-800  --}}
