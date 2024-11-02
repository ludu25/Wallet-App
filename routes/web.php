<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;

Route::get('/', [AuthController::class, 'show']);
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.auth');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::post('/validate_phone', [HomeController::class, 'validatePhoneNumber'])->name('validate_phone');
Route::post('/convert_to_usd', [HomeController::class, 'convertToUSD'])->name('convert_to_usd');

require __DIR__.'/auth.php';
