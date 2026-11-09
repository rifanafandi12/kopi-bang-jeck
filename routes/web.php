<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\TableEntry;
use App\Livewire\Customer\MenuOrder;
use App\Livewire\Customer\OrderConfirm;
use App\Livewire\Customer\OrderStatusPage;
use App\Livewire\Cashier\Dashboard;
use App\Livewire\Auth\Login;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/m/{token}', TableEntry::class)->name('customer.entry');
Route::get('/m/{token}/menu', MenuOrder::class)->name('customer.menu');
Route::get('/m/{token}/konfirmasi', OrderConfirm::class)->name('customer.confirm');
Route::get('/m/{token}/status', OrderStatusPage::class)->name('customer.status');
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
Route::middleware(['auth', 'role:kasir|pemilik|admin'])->group(function () {
    Route::get('/kasir', Dashboard::class)->name('cashier.dashboard');
});
