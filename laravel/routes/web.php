<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\JoinController;
use Illuminate\Support\Facades\Route;

Route::get('/join', [JoinController::class, 'create'])->name('join');
Route::post('/join', [JoinController::class, 'store'])->name('join.store');
Route::get('/locations/{state}/lgas', [JoinController::class, 'lgas'])->name('locations.lgas');
Route::get('/locations/{lga}/wards', [JoinController::class, 'wards'])->name('locations.wards');

Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});
