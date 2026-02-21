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

use App\Http\Controllers\TimeController;
use App\Http\Controllers\AtletaController;
use App\Http\Controllers\EquipesController;
use App\Http\Controllers\EquipeCampeonatoController;
use App\Http\Controllers\GinasioController;
use App\Http\Controllers\ComissaoTecnicaController;


Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    // ---------------------------------------------------------------------
    // 1. COMMOM ROUTES (All Authenticated Users: Admin, RespTime, Juiz, Apontador)
    // ---------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [JogosController::class, 'index_dashboard'])->name('dashboard');

    // Jogos (Index/Show is restricted by logic in Controller, so safe for all)
    // Create/Edit/Delete should be protected? Controller 'store'/'update' checks permissions?
    // JogosController checks: index (filtered), create/store/edit/update (Juiz shouldn't access?, well, Juiz writes results).
    // Let's assume JogosController handles fine-grained access or we leave it open for now as requested "Juiz visualiza jogos".
    Route::resource('jogos', JogosController::class);


    // Resultados (Scorer/Apontador needs access)
    Route::get('/jogos/{id}/resultado/create', [App\Http\Controllers\ResultadosController::class, 'create'])->name('resultados.create');
    Route::post('/jogos/{id}/resultado', [App\Http\Controllers\ResultadosController::class, 'store'])->name('resultados.store');
    Route::patch('/jogos/{id}/approve', [App\Http\Controllers\ResultadosController::class, 'approve'])->name('resultados.approve');

    // Documentos (Visível para todos autenticados)
    Route::get('/documentos', [App\Http\Controllers\DocumentoController::class, 'index'])->name('documentos.index');

    // Ginásios (Visível para todos autenticados)
    Route::get('/ginasios', [GinasioController::class, 'index'])->name('ginasios.index');


    // ---------------------------------------------------------------------
    // 2. ADMIN + RESPONSAVEl TIME ONLY (Juiz/Apontador CANNOT Access)
    // ---------------------------------------------------------------------
    Route::middleware(['role:Administrador|ResponsavelTime|ComissaoTecnica'])->group(function () {

        Route::patch('times/{time}/inactivate', [TimeController::class, 'inactivate'])->name('times.inactivate');
        Route::resource('times', TimeController::class);

        Route::get('atletas/{atleta}/print', [AtletaController::class, 'print'])->name('atletas.print');
        Route::patch('atletas/{atleta}/mark-printed', [AtletaController::class, 'markPrinted'])->name('atletas.markPrinted');
        Route::patch('atletas/{atleta}/inactivate', [AtletaController::class, 'inactivate'])->name('atletas.inactivate');
        Route::resource('atletas', AtletaController::class);

        Route::get('equipes/time/{time}', [EquipesController::class, 'indexForTime'])->name('equipes.index.for.time');
        Route::resource('equipes', EquipesController::class);
        Route::patch('comissao-tecnica/{id}/mark-printed', [ComissaoTecnicaController::class, 'markPrinted'])->name('comissao-tecnica.markPrinted');
        Route::patch('comissao-tecnica/{id}/toggle-status', [ComissaoTecnicaController::class, 'toggleStatus'])->name('comissao-tecnica.toggleStatus');
        Route::resource('comissao-tecnica', ComissaoTecnicaController::class);

        Route::get('campeonatos/{campeonato}/equipes', [EquipeCampeonatoController::class, 'index'])->name('equipes.campeonato.index');
        Route::get('campeonatos/{campeonato}/equipes/create', [EquipeCampeonatoController::class, 'create'])->name('equipes.campeonato.create');
        Route::post('campeonatos/{campeonato}/equipes', [EquipeCampeonatoController::class, 'store'])->name('equipes.campeonato.store');
        Route::delete('campeonatos/{campeonato}/equipes/{equipe}', [EquipeCampeonatoController::class, 'destroy'])->name('equipes.campeonato.destroy');

        Route::get('elencos', [App\Http\Controllers\ElencoController::class, 'list'])->name('elenco.list');
        Route::get('/api/campeonatos/{id}/equipes', [EquipeCampeonatoController::class, 'listByCampeonatoJson'])->name('api.equipes.campeonato');

        Route::prefix('campeonatos/{campeonato}/equipes/{equipe_campeonato}/elenco')->group(function () {
            Route::get('/', [App\Http\Controllers\ElencoController::class, 'index'])->name('elenco.index');
            Route::post('/', [App\Http\Controllers\ElencoController::class, 'store'])->name('elenco.store');
            Route::delete('/{elenco_id}', [App\Http\Controllers\ElencoController::class, 'destroy'])->name('elenco.destroy');
        });

    });


    // ---------------------------------------------------------------------
    // 3. ADMIN ONLY (Restricted for everyone else)
    // ---------------------------------------------------------------------
    Route::middleware(['role:Administrador'])->group(function () {

        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        Route::resource('users', UserController::class);

        Route::resource('categorias', CategoriasController::class);
        Route::get('/ginasios/create', [GinasioController::class, 'create'])->name('ginasios.create');
        Route::patch('/ginasios/{ginasio}/toggle-status', [GinasioController::class, 'toggleStatus'])->name('ginasios.toggleStatus');
        Route::resource('ginasios', GinasioController::class)->except(['index']);
        Route::resource('eventos', TiposEventosController::class);

        Route::get('/test-create-roles', [JogosController::class, 'test']);

        Route::resource('role-permission', RolePermissionController::class)->parameters([
            'role-permission' => 'role'
        ]);

        Route::resource('profiles', UserProfileController::class);
        Route::resource('profile_user', ProfileUserController::class);

        Route::get('/jogos/import', [JogosController::class, 'show'])->name('jogos.showImportForm');
        Route::post('/jogos/import', [JogosController::class, 'import'])->name('jogos.import');

        // CRUD Documentos (Admin)
        Route::resource('documentos', App\Http\Controllers\DocumentoController::class)->except(['index', 'show']);


    });

    // ---------------------------------------------------------------------
    // 4. JUIZ ONLY
    // ---------------------------------------------------------------------
    Route::middleware(['role:Juiz'])->group(function () {
        Route::get('/arbitros/{id}', [App\Http\Controllers\ArbitroController::class, 'show'])->name('arbitros.show');
        Route::get('/arbitros', [App\Http\Controllers\ArbitroController::class, 'index'])->name('arbitros.index');
    });

});

require __DIR__ . '/auth.php';
