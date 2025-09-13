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
<body class="flex justify-center bg-neutral-100 p-24 h-screen">
    <div class="flex min-w-0 max-w-6xl flex-auto flex-row overflow-hidden bg-neutral-200 rounded-2xl">
     <aside class="pr-3 flex flex-col items-center justify-start py-6 w-14 backdrop-blur-3xl gap-4">
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
     <main class="flex-1 flex flex-col m-2 bg-neutral-50 rounded-2xl shadow-xs border border-accent/20">
        <flux:main class="p-0!">
            {{ $slot }}
        </flux:main>
     </main>
    </div>
    @fluxScripts
</body>
</html>
