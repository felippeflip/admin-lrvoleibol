<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\TiposEventosController;
use App\Http\Controllers\JogosController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ProfileUserController;




Route::get('/', function () {
    return view('auth.login');
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [JogosController::class, 'index_dashboard'])->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('categorias', CategoriasController::class);

    Route::resource('eventos', TiposEventosController::class);

    Route::resource('jogos', JogosController::class);

    Route::get('/test-create-roles', [JogosController::class, 'test']);

    Route::resource('role-permission', RolePermissionController::class)->parameters([
        'role-permission' => 'role'
    ]);

    Route::resource('profiles', UserProfileController::class);

    Route::resource('profile_user', ProfileUserController::class);

    Route::get('/jogos/import', [JogosController::class, 'show'])->name('jogos.showImportForm');
    Route::post('/jogos/import', [JogosController::class, 'import'])->name('jogos.import');
});

require __DIR__.'/auth.php';
