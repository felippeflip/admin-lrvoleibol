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
use App\Http\Controllers\FuncoesController;




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
    Route::get('/resultados/import', [FuncoesController::class, 'showImport'])->name('resultados.showImportForm');
    Route::post('/resultados/upload', [FuncoesController::class, 'upload'])->name('resultados.upload');

    /**
     * Projeto Futuro: Inserir mais de um jogo no mesmo submit
     */
   // Route::get('/multi-insert', [JogosController::class, 'createMulti'])->name('multi.insert');
   // Route::post('/multi-insert', 'MultiInsertController@store')->name('multi.insert.store');
});



require __DIR__.'/auth.php';
