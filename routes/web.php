<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::livewire('/categories', 'pages::category.index')
    ->middleware(['auth'])
    ->name('category.index');

 Route::livewire('/transactions', 'pages::transaction.index')
    ->middleware(['auth'])
    ->name('transaction.index');

 Route::livewire('/transaction-details', 'pages::transaction-details.index')
    ->middleware(['auth'])
    ->name('transaction-details.index');

    
    
require __DIR__.'/settings.php';
