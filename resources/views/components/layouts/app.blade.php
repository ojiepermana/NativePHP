<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<body class="flex justify-center h-screen overflow-hidden ">
    <div class="flex min-w-0 flex-auto flex-row overflow-hidden inset-0 backdrop-blur-2xl bg-white/85 h-full">
     <aside class="pl-2 flex flex-col items-center justify-center w-18 space-y-2 h-full">
        <!-- Icon 1 - Home -->
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-colors cursor-pointer">
            <flux:icon icon="house" class="w-5 h-5 text-gray-600" />
        </div>

        <!-- Icon 2 - Search -->
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-colors cursor-pointer">
            <flux:icon icon="search" class="w-5 h-5 text-gray-600" />
        </div>

        <!-- Icon 3 - Settings -->
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-colors cursor-pointer">
            <flux:icon icon="settings" class="w-5 h-5 text-gray-600" />
        </div>

        <!-- Icon 4 - User -->
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center hover:bg-white/30 transition-colors cursor-pointer">
            <flux:icon icon="user" class="w-5 h-5 text-gray-600" />
        </div>
     </aside>
     <main class="flex-1 flex flex-col my-2 ml-2 mr-2  bg-white border border-neutral-200 rounded-xl shadow-xl">
         <flux:main class="p-0! flex items-stretch overflow-hidden h-full min-h-0">
             <div id="sidebar" class="w-72 shrink-0 sticky top-0 p-4 flex flex-col  h-full border-r border-neutral-200  ">
            asasaassa
            </div>
                <div id="content" class="flex-1 overflow-auto   h-full min-h-0">
                {{ $slot }}
            </div>
        </flux:main>
     </main>
    </div>
    @fluxScripts
</body>
</html>
