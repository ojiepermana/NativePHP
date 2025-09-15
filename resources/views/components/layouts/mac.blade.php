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

        // NativePHP Window Control Functions
        function closeApp() {
            fetch('/native/close', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => console.log('Close requested:', data))
                .catch(error => console.error('Error:', error));
        }

        function minimizeApp() {
            fetch('/native/minimize', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => console.log('Minimize requested:', data))
                .catch(error => console.error('Error:', error));
        }

        function maximizeApp() {
            fetch('/native/maximize', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => console.log('Maximize requested:', data))
                .catch(error => console.error('Error:', error));
        }

        // Alternative direct approach using window methods if available
        document.addEventListener('DOMContentLoaded', function() {
            // Try different possible API methods
            if (typeof window !== 'undefined') {
                // Check for electron APIs
                if (window.require) {
                    const { remote } = window.require('electron');
                    if (remote) {
                        window.closeApp = () => remote.getCurrentWindow().close();
                        window.minimizeApp = () => remote.getCurrentWindow().minimize();
                        window.maximizeApp = () => {
                            const win = remote.getCurrentWindow();
                            if (win.isMaximized()) {
                                win.unmaximize();
                            } else {
                                win.maximize();
                            }
                        };
                    }
                }

                // Check for NativePHP specific APIs
                if (window.nativephp) {
                    window.closeApp = () => window.nativephp.closeWindow();
                    window.minimizeApp = () => window.nativephp.minimizeWindow();
                    window.maximizeApp = () => window.nativephp.maximizeWindow();
                }
            }
        });
    </script>
</head>
<body class="flex justify-center h-screen overflow-hidden backdrop-blur-2xl bg-white/50 dark:bg-black/50">
    <div class="relative flex min-w-0 flex-auto flex-row overflow-hidden  h-full">
         <!-- Drag area at the top of main -->
        <div class="app-drag ml-18 absolute h-5 -mt-2.5 min-w-0 rounded-t-2xl w-full">&nbsp;</div>

    <aside class="pl-2 w-16 h-full flex flex-col items-center justify-between py-4">
         <!-- macOS window controls -->
        <div class="absolute top-2 left-3 z-10 flex items-center gap-2 select-none">
            <!-- Close button (red) -->
            <button onclick="closeApp()"
                    class="w-3 h-3 rounded-full bg-[#ff5f56] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#ff4136] transition-colors cursor-pointer flex items-center justify-center group"
                    title="Close">
                <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">×</span>
            </button>

            <!-- Minimize button (yellow) -->
            <button onclick="minimizeApp()"
                    class="w-3 h-3 rounded-full bg-[#ffbd2e] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#ffb000] transition-colors cursor-pointer flex items-center justify-center group"
                    title="Minimize">
                <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">−</span>
            </button>

            <!-- Maximize/Restore button (green) -->
            <button onclick="maximizeApp()"
                    class="w-3 h-3 rounded-full bg-[#27c93f] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#00d515] transition-colors cursor-pointer flex items-center justify-center group"
                    title="Maximize">
                <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">+</span>
            </button>
        </div>
        <div class="flex flex-col items-center gap-2 pt-4">
            <!-- Icon 1 - Home -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center hover:bg-white/30 dark:hover:bg-white/15 transition-colors cursor-pointer">
                <flux:icon icon="house" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
            </div>

            <!-- Icon 2 - Search -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center hover:bg-white/30 dark:hover:bg-white/15 transition-colors cursor-pointer">
                <flux:icon icon="search" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
            </div>

            <!-- Icon 3 - Settings -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center hover:bg-white/30 dark:hover:bg-white/15 transition-colors cursor-pointer">
                <flux:icon icon="settings" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
            </div>

            <!-- Icon 4 - User -->
            <div class="w-12 h-12 rounded-xl flex items-center justify-center hover:bg-white/30 dark:hover:bg-white/15 transition-colors cursor-pointer">
                <flux:icon icon="user" class="w-6 h-6 text-gray-600 dark:text-gray-300" />
            </div>
        </div>
        <div class="mt-auto flex items-center justify-center">
            <flux:switch x-data x-model="$flux.dark" class="rotate-90" />
        </div>

     </aside>
     <main class="flex-1 flex flex-col my-2 ml-2 mr-2 rounded-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800 shadow-sm  bg-white/75 dark:bg-black/50">

         <flux:main class="p-0! flex items-stretch overflow-hidden flex-1 min-h-0 ">
             <div id="sidebar" class="w-72 shrink-0 sticky top-0 p-4 flex flex-col h-full border-r border-neutral-200 dark:border-neutral-800">
            Sidebar Content
            </div>
                <div id="content" class="flex-1 overflow-auto   h-full min-h-0 bg-white dark:bg-black/50">
                {{ $slot }}
            </div>
        </flux:main>
     </main>
    </div>
    @fluxScripts
</body>
</html>

{{-- rounded-xl shadow-xl --}}
{{-- border border-neutral-200 dark:border-neutral-800  --}}
