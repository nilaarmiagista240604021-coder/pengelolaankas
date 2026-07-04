<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalCategory'          => Category::count(),
            'totalTransaction'       => Transaction::count(),
            'totalTransactionDetail' => TransactionDetail::count(),
            'latestTransactions'     => Transaction::latest()->take(5)->get(),
        ]);
    })->name('dashboard');

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