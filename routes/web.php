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
use App\Http\Controllers\TimeController;
use App\Http\Controllers\AtletaController;
use App\Http\Controllers\EquipesController;
use App\Http\Controllers\EquipeCampeonatoController;
use App\Http\Controllers\GinasioController;




Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [JogosController::class, 'index_dashboard'])->name('dashboard');

    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::resource('users', UserController::class);

    Route::resource('categorias', CategoriasController::class);

    Route::resource('times', TimeController::class);
    Route::resource('ginasios', GinasioController::class);

    Route::patch('atletas/{atleta}/inactivate', [AtletaController::class, 'inactivate'])->name('atletas.inactivate');
    Route::resource('atletas', AtletaController::class);

    Route::resource('eventos', TiposEventosController::class);

    Route::resource('jogos', JogosController::class);

    Route::get('equipes/time/{time}', [EquipesController::class, 'indexForTime'])->name('equipes.index.for.time');

    Route::resource('equipes', EquipesController::class);

    Route::get('campeonatos/{campeonato}/equipes', [EquipeCampeonatoController::class, 'index'])->name('equipes.campeonato.index');
    Route::get('campeonatos/{campeonato}/equipes/create', [EquipeCampeonatoController::class, 'create'])->name('equipes.campeonato.create');
    Route::post('campeonatos/{campeonato}/equipes', [EquipeCampeonatoController::class, 'store'])->name('equipes.campeonato.store');
    Route::delete('campeonatos/{campeonato}/equipes/{equipe}', [EquipeCampeonatoController::class, 'destroy'])->name('equipes.campeonato.destroy');



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

   
});



require __DIR__.'/auth.php';
