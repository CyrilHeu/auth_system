<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmptyPageController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'show'])
        ->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'send'])
        ->name('password.email');
});

Route::middleware('backoffice.auth')->group(function (): void {
    Route::get('/app', EmptyPageController::class)->name('app');
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
});
