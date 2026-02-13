<?php

use App\Http\Controllers\NativeController;
use App\Livewire\Pages\DashboardPage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Native window control routes
Route::post('/native/close', [NativeController::class, 'close'])->name('native.close');
Route::post('/native/minimize', [NativeController::class, 'minimize'])->name('native.minimize');
Route::post('/native/maximize', [NativeController::class, 'maximize'])->name('native.maximize');

// Root route - redirect based on authentication status
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::livewire('/auth/verify', \App\Livewire\Auth\Verify::class)->name('auth.verify');

    // Social login routes
    Route::get('/auth/google', [
        App\Http\Controllers\SocialAuthController::class,
        'redirectToGoogle',
    ])->name('auth.google');
    Route::get('/auth/google/callback', [
        App\Http\Controllers\SocialAuthController::class,
        'handleGoogleCallback',
    ])->name('auth.google.callback');

    Route::get('/auth/microsoft', [
        App\Http\Controllers\SocialAuthController::class,
        'redirectToMicrosoft',
    ])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [
        App\Http\Controllers\SocialAuthController::class,
        'handleMicrosoftCallback',
    ])->name('auth.microsoft.callback');

    Route::get('/auth/apple', [
        App\Http\Controllers\SocialAuthController::class,
        'redirectToApple',
    ])->name('auth.apple');
    Route::get('/auth/apple/callback', [
        App\Http\Controllers\SocialAuthController::class,
        'handleAppleCallback',
    ])->name('auth.apple.callback');
});

// Authenticated  routes
Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', DashboardPage::class)->name('dashboard');

// Job Billing routes
    Route::livewire('/job/billing/{status}', \App\Livewire\Pages\Job\JobBillingPage::class)
        ->name('job.billing')
        ->where('status', 'belum|lengkap|selesai');

    // Billing Status routes
    Route::livewire('/billing/status/{status}', \App\Livewire\Pages\Billing\BillingStatusPage::class)
        ->name('billing.status')
        ->where('status', 'belum|bermasalah|dibatalkan|selesai|verifikasi|arsip|digital|faktur');

    // Invoice E-Invoice routes
    Route::livewire('/invoice/proses/{status}', \App\Livewire\Pages\Invoice\InvoiceProsesStatusPage::class)
        ->name('invoice.proses')
        ->where('status', 'draft|proses|selesai|laporan');

    // Invoice E-Invoice Detail route
    Route::livewire('/invoice/proses/proses/detail/{nomor}', \App\Livewire\Pages\Invoice\InvoiceProsesDetailPage::class)
        ->name('invoice.proses.detail');

    // Invoice Print route
    Route::get('/invoice/print/{id_faktur}', [
        App\Http\Controllers\InvoicePrintController::class,
        'show',
    ])->name('invoice.print');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
