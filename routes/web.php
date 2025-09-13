<?php

use App\Livewire\Pages\DashboardPage;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardPage::class)->name('index');
