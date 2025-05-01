<?php

use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SeleksiController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Siswa Routes
    Route::resource('siswa', SiswaController::class);
    Route::resource('seleksi',SeleksiController::class);
    Route::delete('/siswa-batch', [SiswaController::class, 'batchDelete']);

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
