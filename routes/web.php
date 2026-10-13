<?php

use App\Http\Controllers\AdminSurveyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home'));

// Encuesta pública: no requiere autenticación.
Route::get('/encuesta/{slug}', [SurveyController::class, 'show'])->name('survey.show');
Route::post('/encuesta/{slug}', [SurveyController::class, 'store'])->name('survey.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admin/encuestas/{slug}/resultados', [AdminSurveyController::class, 'results'])->name('admin.surveys.results');
});
