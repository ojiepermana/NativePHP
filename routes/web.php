<?php

use App\Livewire\Pages\DashboardPage;
use App\Http\Controllers\NativeController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardPage::class)->name('index');

// Native window control routes
Route::post('/native/close', [NativeController::class, 'close'])->name('native.close');
Route::post('/native/minimize', [NativeController::class, 'minimize'])->name('native.minimize');
Route::post('/native/maximize', [NativeController::class, 'maximize'])->name('native.maximize');
