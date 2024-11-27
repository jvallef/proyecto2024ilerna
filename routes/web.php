<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\TestAvatarController; // Agregado
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
    Route::get('/profile/avatar', function () {
        return view('profile.avatar');
    })->name('profile.avatar');

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

    // Ruta para la página de prueba de uploads
    Route::get('/test-upload', function () {
        return view('test-upload');
    })->middleware(['auth', 'verified'])->name('test.upload');

    // Rutas para el controlador de subida
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

    // Rutas para archivos y medios
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Rutas de prueba
    Route::prefix('tests')->group(function () {
        Route::get('/media-upload', function () {
            return view('tests.components.media-upload');
        })->name('media-upload');

        // Rutas de prueba para componentes de media
        Route::prefix('media')->group(function () {
            Route::get('/single-image', function () {
                return view('tests.media.single-image');
            })->name('single-image');
            
            Route::get('/multiple-images', function () {
                return view('tests.media.multiple-images');
            })->name('multiple-images');
            
            Route::get('/single-file', function () {
                return view('tests.media.single-file');
            })->name('single-file');
            
            Route::get('/multiple-files', function () {
                return view('tests.media.multiple-files');
            })->name('multiple-files');
            
            Route::get('/form', function () {
                return view('tests.media.single-image-form');
            })->middleware(['auth', 'verified'])->name('form');

            // Rutas para el test de avatar
            Route::get('avatar', [TestAvatarController::class, 'create'])->name('media.avatar');
            Route::post('avatar', [TestAvatarController::class, 'store'])->name('media.avatar.store');
        });
    });

    // Rutas antiguas (mantener temporalmente para compatibilidad)
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::delete('/files/{filename}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::delete('/images/{filename}', [ImageController::class, 'destroy'])->name('images.destroy');
});

require __DIR__.'/auth.php';
