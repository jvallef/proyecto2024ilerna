<?php

use App\Http\Controllers\TestAvatarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\PathController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Api\UserSearchController;
use App\Http\Controllers\Api\AreaSearchController;
use App\Http\Controllers\Api\AreaTrashedSearchController;
use App\Http\Controllers\Api\PathSearchController;
use App\Http\Controllers\Api\PathTrashedSearchController;
use App\Http\Controllers\Api\CourseSearchController;
use App\Http\Controllers\Api\CourseTrashedSearchController;
use App\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas públicas de áreas
Route::get('/areas', [AreaController::class, 'publicIndex'])->name('areas.index');
Route::get('/areas/{slug}', [AreaController::class, 'publicShow'])->name('areas.show');

// Rutas públicas de paths
Route::get('/paths', [PathController::class, 'publicIndex'])->name('paths.index');
Route::get('/paths/{slug}', [PathController::class, 'publicShow'])->name('paths.show');

// Rutas públicas de courses
Route::get('/courses', [CourseController::class, 'publicIndex'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'publicShow'])->name('courses.show');

// Rutas de búsqueda de áreas
Route::prefix('api')->group(function () {
    // Ruta pública de búsqueda
    Route::get('/search/areas/public', [\App\Http\Controllers\Api\AreaSearchController::class, 'suggestions'])
        ->name('api.areas.search.public');
        
    // Ruta pública de búsqueda de paths
    Route::get('/search/paths/public', [\App\Http\Controllers\Api\PathSearchController::class, 'suggestions'])
        ->name('api.paths.search.public');

    // Ruta pública de búsqueda de courses
    Route::get('/search/courses/public', [\App\Http\Controllers\Api\CourseSearchController::class, 'suggestions'])
        ->name('api.courses.search.public');
});

// Rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {
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
        Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
        Route::put('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        
        // Rutas admin de áreas
        Route::get('areas/trashed', [AreaController::class, 'privateTrashed'])->name('areas.trashed');
        Route::get('areas/create', [AreaController::class, 'privateCreate'])->name('areas.create');
        Route::get('areas/{area}/edit', [AreaController::class, 'privateEdit'])->name('areas.edit');
        Route::patch('areas/{area}/restore', [AreaController::class, 'privateRestore'])->name('areas.restore');
        Route::delete('areas/{area}/force-delete', [AreaController::class, 'privateForceDelete'])->name('areas.force-delete');
        
        Route::get('areas', [AreaController::class, 'privateIndex'])->name('areas.index');
        Route::post('areas', [AreaController::class, 'privateStore'])->name('areas.store');
        Route::match(['put', 'patch'], 'areas/{area}', [AreaController::class, 'privateUpdate'])->name('areas.update');
        Route::delete('areas/{area}', [AreaController::class, 'privateDestroy'])->name('areas.destroy');
        Route::get('areas/{area}', [AreaController::class, 'privateShow'])->name('areas.show');
        
        // Rutas admin de paths
        Route::get('paths/trashed', [PathController::class, 'privateTrashed'])->name('paths.trashed');
        Route::get('paths/create', [PathController::class, 'privateCreate'])->name('paths.create');
        Route::get('paths/{path}/edit', [PathController::class, 'privateEdit'])->name('paths.edit');
        Route::patch('paths/{path}/restore', [PathController::class, 'privateRestore'])->name('paths.restore');
        Route::delete('paths/{path}/force-delete', [PathController::class, 'privateForceDelete'])->name('paths.force-delete');
        
        Route::get('paths', [PathController::class, 'privateIndex'])->name('paths.index');
        Route::post('paths', [PathController::class, 'privateStore'])->name('paths.store');
        Route::match(['put', 'patch'], 'paths/{path}', [PathController::class, 'privateUpdate'])->name('paths.update');
        Route::delete('paths/{path}', [PathController::class, 'privateDestroy'])->name('paths.destroy');
        Route::get('paths/{path}', [PathController::class, 'privateShow'])->name('paths.show');

        // Rutas admin de courses
        Route::get('courses/trashed', [CourseController::class, 'privateTrashed'])->name('courses.trashed');
        Route::get('courses/create', [CourseController::class, 'privateCreate'])->name('courses.create');
        Route::get('courses/{course}/edit', [CourseController::class, 'privateEdit'])->name('courses.edit');
        Route::patch('courses/{course}/restore', [CourseController::class, 'privateRestore'])->name('courses.restore');
        Route::delete('courses/{course}/force-delete', [CourseController::class, 'privateForceDelete'])->name('courses.force-delete');
        
        Route::get('courses', [CourseController::class, 'privateIndex'])->name('courses.index');
        Route::post('courses', [CourseController::class, 'privateStore'])->name('courses.store');
        Route::match(['put', 'patch'], 'courses/{course}', [CourseController::class, 'privateUpdate'])->name('courses.update');
        Route::delete('courses/{course}', [CourseController::class, 'privateDestroy'])->name('courses.destroy');
        Route::get('courses/{course}', [CourseController::class, 'privateShow'])->name('courses.show');
        
        // Rutas de contenido
        Route::get('/contents', [ContentController::class, 'index'])->name('contents.index');
        Route::get('/contents/create', [ContentController::class, 'create'])->name('contents.create');
        Route::get('/contents/{content}', [ContentController::class, 'show'])->name('contents.show');
        Route::post('/contents/preview', [ContentController::class, 'preview'])->name('contents.preview');
        Route::post('/contents', [ContentController::class, 'store'])->name('contents.store');
        
        // API de búsqueda
        Route::get('/api/search/users', [UserSearchController::class, 'suggestions'])
            ->name('api.users.search');
        Route::get('/api/search/areas', [AreaSearchController::class, 'suggestions'])
            ->name('api.areas.search');
        Route::get('/api/search/areas/trashed', [AreaTrashedSearchController::class, 'suggestions'])
            ->name('api.areas.trashed.search');
        Route::get('/api/search/paths', [PathSearchController::class, 'suggestions'])
            ->name('api.paths.search');
        Route::get('/api/search/paths/trashed', [PathTrashedSearchController::class, 'suggestions'])
            ->name('api.paths.trashed.search');
        Route::get('/api/search/courses', [CourseSearchController::class, 'suggestions'])
            ->name('api.courses.search');
        Route::get('/api/search/courses/trashed', [CourseTrashedSearchController::class, 'suggestions'])
            ->name('api.courses.trashed.search');
    });

    // Rutas de profesor
    Route::middleware(['role:teacher'])->prefix('workarea')->name('workarea.')->group(function () {
        Route::get('/', [DashboardController::class, 'teacher'])->name('dashboard');
        
        // Rutas educativas de áreas para profesores
        Route::get('areas', [AreaController::class, 'educaIndex'])->name('areas.index');
        Route::get('areas/{slug}', [AreaController::class, 'educaShow'])->name('areas.show');
        Route::get('areas/{slug}/progress', [AreaController::class, 'educaProgress'])->name('areas.progress');
        
        // Rutas educativas de paths para profesores
        Route::get('paths', [PathController::class, 'educaIndex'])->name('paths.index');
        Route::get('paths/{slug}', [PathController::class, 'educaShow'])->name('paths.show');
        Route::get('paths/{slug}/progress', [PathController::class, 'educaProgress'])->name('paths.progress');

        // Rutas educativas de courses para profesores
        Route::get('courses', [CourseController::class, 'educaIndex'])->name('courses.index');
        Route::get('courses/{slug}', [CourseController::class, 'educaShow'])->name('courses.show');
        Route::get('courses/{slug}/progress', [CourseController::class, 'educaProgress'])->name('courses.progress');
        Route::post('courses/{course}/enroll', [CourseController::class, 'enrollStudent'])->name('courses.enroll');
        Route::delete('courses/{course}/unenroll', [CourseController::class, 'unenrollStudent'])->name('courses.unenroll');
    });

    // Rutas de estudiante
    Route::middleware(['role:student'])->prefix('classroom')->name('classroom.')->group(function () {
        Route::get('/', [DashboardController::class, 'student'])->name('dashboard');
        
        // Rutas educativas de áreas para estudiantes
        Route::get('areas', [AreaController::class, 'educaIndex'])->name('areas.index');
        Route::get('areas/{slug}', [AreaController::class, 'educaShow'])->name('areas.show');
        Route::get('areas/{slug}/progress', [AreaController::class, 'educaProgress'])->name('areas.progress');
        
        // Rutas educativas de paths para estudiantes
        Route::get('paths', [PathController::class, 'educaIndex'])->name('paths.index');
        Route::get('paths/{slug}', [PathController::class, 'educaShow'])->name('paths.show');
        Route::get('paths/{slug}/progress', [PathController::class, 'educaProgress'])->name('paths.progress');

        // Rutas educativas de courses para estudiantes
        Route::get('courses', [CourseController::class, 'educaIndex'])->name('courses.index');
        Route::get('courses/{slug}', [CourseController::class, 'educaShow'])->name('courses.show');
        Route::get('courses/{slug}/progress', [CourseController::class, 'educaProgress'])->name('courses.progress');
        Route::post('courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::delete('courses/{course}/unenroll', [CourseController::class, 'unenroll'])->name('courses.unenroll');
    });

    // Ruta para la página de prueba de uploads
    Route::get('/test-upload', function () {
        return view('test-upload');
    })->middleware(['auth', 'verified'])->name('test.upload');

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
            })->middleware(['verified'])->name('form');

            // Rutas para el test de avatar
            Route::get('avatar', [TestAvatarController::class, 'create'])->name('media.avatar');
            Route::post('avatar', [TestAvatarController::class, 'store'])->name('media.avatar.store');

            // Rutas para la creación de usuario
            Route::get('/user', [UserController::class, 'create'])->name('test.user.create');
            Route::post('/user', [UserController::class, 'store'])->name('test.user.store');
    
        });
    });

    // Rutas antiguas (mantener temporalmente para compatibilidad)
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::delete('/files/{filename}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::delete('/images/{filename}', [ImageController::class, 'destroy'])->name('images.destroy');
});

require __DIR__.'/auth.php';
