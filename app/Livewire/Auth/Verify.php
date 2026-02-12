<?php

namespace App\Livewire\Auth;

use App\Models\LoginToken;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Verify extends Component
{
    public string $token = '';

    public string $status = 'verifying';

    public function mount(): void
    {
        $this->token = request()->query('token', '');

        if (! $this->token) {
            $this->status = 'invalid';

            return;
        }

        $loginToken = LoginToken::where('token', $this->token)->first();

        if (! $loginToken) {
            $this->status = 'invalid';

            return;
        }

        if (! $loginToken->isValid()) {
            $this->status = $loginToken->used_at ? 'invalid' : 'expired';

            return;
        }

        // Mark token as used
        $loginToken->markAsUsed();

        // Log the user in
        Auth::login($loginToken->user, true);

        $this->status = 'success';

        // Redirect after a brief moment
        $this->dispatch('verified');
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return <<<'HTML'
<div
  class="flex min-h-screen items-center justify-center bg-linear-to-br from-blue-50 to-indigo-100 px-4 dark:from-neutral-900 dark:to-neutral-800"
>
  <div class="w-full max-w-md">
    <flux:card class="p-8">
      @if ($status === 'verifying')
        <div class="text-center">
          <div class="mb-4 flex justify-center">
            <flux:icon
              icon="arrow-path"
              class="h-16 w-16 animate-spin text-blue-600 dark:text-blue-400"
            />
          </div>
          <flux:heading size="lg" class="mb-2">Memverifikasi...</flux:heading>
          <flux:text class="text-neutral-600 dark:text-neutral-400">
            Mohon tunggu sebentar
          </flux:text>
        </div>
      @elseif ($status === 'success')
        <div class="text-center">
          <div class="mb-4 flex justify-center">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
            >
              <flux:icon icon="check" class="h-10 w-10 text-green-600 dark:text-green-400" />
            </div>
          </div>
          <flux:heading size="lg" class="mb-2">Login Berhasil!</flux:heading>
          <flux:text class="mb-6 text-neutral-600 dark:text-neutral-400">
            Anda akan dialihkan ke dashboard...
          </flux:text>
          <flux:button variant="primary" href="/" wire:navigate class="w-full">
            Ke Dashboard
          </flux:button>
        </div>
      @elseif ($status === 'expired')
        <div class="text-center">
          <div class="mb-4 flex justify-center">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30"
            >
              <flux:icon icon="clock" class="h-10 w-10 text-orange-600 dark:text-orange-400" />
            </div>
          </div>
          <flux:heading size="lg" class="mb-2">Link Kadaluarsa</flux:heading>
          <flux:text class="mb-6 text-neutral-600 dark:text-neutral-400">
            Magic link Anda telah kadaluarsa. Silakan minta link baru.
          </flux:text>
          <flux:button variant="primary" href="/login" wire:navigate class="w-full">
            Kembali ke Login
          </flux:button>
        </div>
      @else
        <div class="text-center">
          <div class="mb-4 flex justify-center">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
            >
              <flux:icon icon="x-circle" class="h-10 w-10 text-red-600 dark:text-red-400" />
            </div>
          </div>
          <flux:heading size="lg" class="mb-2">Link Tidak Valid</flux:heading>
          <flux:text class="mb-6 text-neutral-600 dark:text-neutral-400">
            Magic link tidak valid atau sudah pernah digunakan.
          </flux:text>
          <flux:button variant="primary" href="/login" wire:navigate class="w-full">
            Kembali ke Login
          </flux:button>
        </div>
      @endif
    </flux:card>
  </div>
</div>

<script>
  document.addEventListener('livewire:init', () => {
    Livewire.on('verified', () => {
      setTimeout(() => {
        window.location.href = '/'
      }, 2000)
    })
  })
</script>

HTML;
    }
}
