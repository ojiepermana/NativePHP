@props([
    'title' => 'Tidak Ada Data',
    'message' => 'Tidak ada data yang tersedia',
    'showReset' => false,
    'resetAction' => null,
])

<div class="flex h-full min-h-screen flex-col items-center justify-center bg-white dark:bg-black">
    <div class="flex flex-col items-center gap-8 text-center max-w-2xl px-6">
        <!-- Illustration -->
        <div class="w-64 h-64 sm:w-96 sm:h-96 lg:w-[32rem] lg:h-[32rem]">
            <x-illustrations.empty-documents class="w-full h-full" />
        </div>

        <!-- Content -->
        <div class="space-y-4">
            <flux:heading size="xl" class="text-2xl sm:text-3xl font-semibold">{{ $title }}</flux:heading>
            <flux:text class="text-gray-500 dark:text-gray-400 text-base sm:text-lg">
                {{ $message }}
            </flux:text>

            @if($showReset && $resetAction)
                <div class="pt-4">
                    <flux:button
                        wire:click="{{ $resetAction }}"
                        size="base"
                        variant="filled"
                        class="bg-accent hover:bg-accent/90 text-white"
                    >
                        Reset Filter
                    </flux:button>
                </div>
            @endif
        </div>
    </div>
</div>
