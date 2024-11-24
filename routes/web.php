<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas
Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
Route::get('/areas/{slug}', [AreaController::class, 'show'])->name('areas.show');

// Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para cambiar de rol
    Route::get('/switch-role/{role}', [RoleSwitchController::class, 'switch'])->name('switch.role');

    // Rutas de administración
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('users', UserController::class);
    });

    // Rutas de profesor
    Route::middleware(['role:teacher'])->prefix('workarea')->name('workarea.')->group(function () {
        Route::get('/', [DashboardController::class, 'teacher'])->name('dashboard');
    });

    // Rutas de estudiante
    Route::middleware(['role:student'])->prefix('classroom')->name('classroom.')->group(function () {
        Route::get('/', [DashboardController::class, 'student'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
