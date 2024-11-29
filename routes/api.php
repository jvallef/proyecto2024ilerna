<?php

use App\Http\Controllers\Api\UserSearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de API para administración
Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::get('/search/users', [UserSearchController::class, 'suggestions'])
        ->name('api.users.search');
});
