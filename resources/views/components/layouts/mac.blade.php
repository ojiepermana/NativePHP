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
             <div id="sidebar" class="w-80 shrink-0 sticky top-0  shad-xs flex flex-col items-start h-full border  bg-white/85 rounded-2xl border-neutral-300 dark:border-neutral-800  dark:bg-black/50">
                {{-- border-b  border-neutral-300 dark:border-neutral-800  --}}
                <div id="header" class=" w-full h-10 flex items-center justify-between px-4">
                    <x-mac-window-controls />
                    <flux:switch x-data x-model="$flux.dark"  />
                </div>
                <div id="nav" class="flex-1  p-4">Navigasi</div>
                {{-- border-t  border-neutral-300 dark:border-neutral-800  --}}
                <div id="footer" class=" w-full h-10 px-3">sidebars</div>
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
