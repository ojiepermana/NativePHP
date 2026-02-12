<?php

namespace App\Livewire\Auth;

use App\Mail\MagicLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    protected array $rules = [
        'email' => 'required|email',
    ];

    public function sendMagicLink(): void
    {
        $this->validate();

        // Check user in database
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ]);
        }

        if ($user->isSuspended()) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah di-suspend. Silakan hubungi administrator.',
            ]);
        }

        // Check employee status if employee_id exists
        if ($user->employee_id) {
            $this->validateEmployeeStatus($user->employee_id);
        }

        // Create magic link token
        $loginToken = LoginToken::createForUser($user);

        // Send email with magic link
        Mail::to($user->email)->send(new MagicLinkMail($loginToken));

        session()->flash('success', 'Magic link telah dikirim ke email Anda. Silakan cek inbox Anda.');

        $this->reset('email');
    }

    protected function validateEmployeeStatus(string $employeeId): void
    {
        try {
            $employeeStatus = DB::connection('mysql')
                ->table('erp_hr.pegawai')
                ->where('id_pegawai', $employeeId)
                ->value('status_pegawai');

            if (! $employeeStatus) {
                throw ValidationException::withMessages([
                    'email' => 'Data pegawai tidak ditemukan. Silakan hubungi administrator.',
                ]);
            }

            $allowedStatuses = ['contract', 'permanent'];

            if (! in_array(strtolower($employeeStatus), $allowedStatuses)) {
                throw ValidationException::withMessages([
                    'email' => 'Status pegawai Anda tidak diizinkan untuk login. Silakan hubungi administrator.',
                ]);
            }
        } catch (\Exception $e) {
            // If MySQL connection is not available, allow login to proceed
            // This is useful for development or when MySQL is temporarily unavailable
            if (! $e instanceof ValidationException) {
                return;
            }
            throw $e;
        }
    }

    public function loginDirectly(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $user = User::where('email', 'me@ojiepermana.com')->first();

        if (! $user) {
            session()->flash('error', 'User tidak ditemukan.');

            return;
        }

        auth()->login($user);

        $this->redirect(route('dashboard'), navigate: true);
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
      <div class="mb-8 text-center">
        <flux:heading size="lg" class="mb-2">Selamat Datang</flux:heading>
        <flux:text class="text-neutral-600 dark:text-neutral-400">
          Silakan login untuk mengakses invoice services
        </flux:text>
      </div>

      @if (session('success'))
        <flux:callout variant="success" class="mb-6" icon="check-circle">
          {{ session('success') }}
        </flux:callout>
      @endif

      <!-- Social Login Buttons -->
      <div class="mb-8 space-y-3">
        <a href="{{ route('auth.google') }}" class="flex w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-3 font-medium text-neutral-900 transition hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700">
          <svg class="h-5 w-5" viewBox="0 0 24 24">
            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          <span>Google</span>
        </a>

        <a href="{{ route('auth.microsoft') }}" class="flex w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-3 font-medium text-neutral-900 transition hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700">
          <svg class="h-5 w-5" viewBox="0 0 24 24">
            <path fill="currentColor" d="M11.4 24H0V12.6h11.4V24zM24 24H12.6v-11.4H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
          </svg>
          <span>Microsoft</span>
        </a>

        <a href="{{ route('auth.apple') }}" class="flex w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white px-4 py-3 font-medium text-neutral-900 transition hover:bg-neutral-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:hover:bg-neutral-700">
          <svg class="h-5 w-5" viewBox="0 0 384 512">
            <path fill="currentColor" d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
          </svg>
          <span>Apple</span>
        </a>
      </div>

      <!-- Divider -->
      <div class="mb-6 flex items-center gap-4">
        <div class="flex-1 border-t border-neutral-300 dark:border-neutral-600"></div>
        <span class="text-sm text-neutral-500 dark:text-neutral-400">atau login dengan</span>
        <div class="flex-1 border-t border-neutral-300 dark:border-neutral-600"></div>
      </div>

      <form wire:submit="sendMagicLink">
        <flux:field>
          <flux:label for="email">Email Anda</flux:label>
          <flux:input
            wire:model="email"
            type="email"
            id="email"
            placeholder="nama@email.com"
            autocomplete="email"
          />
          <flux:error name="email" />
        </flux:field>

        <div class="mt-6 flex gap-3">
          <flux:button type="submit" variant="primary" class="@if(app()->environment('local')) flex-[3] @else w-full @endif" wire:loading.attr="disabled">
            <span wire:loading.remove>Kirim  Link</span>
            <span wire:loading>
              <flux:icon icon="arrow-path" class="animate-spin" />
              Mengirim...
            </span>
          </flux:button>
          @if(app()->environment('local'))
            <flux:button type="button" variant="danger" class="flex-1" wire:click="loginDirectly">
              Login
            </flux:button>
          @endif
        </div>
      </form>

      <div class="mt-4 text-center">
        <flux:text class="text-xs text-neutral-500 dark:text-neutral-400">
          Link login akan dikirim ke email Anda dan berlaku selama 15 menit
        </flux:text>
      </div>
    </flux:card>

    <div class="mt-4 text-center">
      <flux:text class="text-sm text-neutral-600 dark:text-neutral-400">
        Invoice Digital Services - ETOS
      </flux:text>
    </div>
  </div>
</div>

HTML;
    }
}
