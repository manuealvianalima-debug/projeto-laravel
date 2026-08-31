<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddTecnologiaController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;
use MrBrownNL\Saml2\Http\Controllers\Saml2Controller;

use App\Models\ActivityLog;
use App\Models\Situacao;
use App\Models\Tecnologias_idiomas;
use App\Models\Unidade;
use App\Models\User;
use App\Support\ActivityLogger;
use MrBrownNL\Saml2\Saml2Auth;


/*
|--------------------------------------------------------------------------
| ROTAS SAML2 (LOGIN FIOCRUZ)
|--------------------------------------------------------------------------


Route::prefix('iam')->group(function () {

    Route::get('/login', function () {
        return app(Saml2Auth::class)->login();
    })->defaults('idpName', 'test')->name('saml2_login');

    Route::post('/acs', function () {
        return app(Saml2Auth::class)->acs();
    })->defaults('idpName', 'test')->name('saml2_acs');

    Route::get('/sls', function () {
        return app(Saml2Auth::class)->sls();
    })->defaults('idpName', 'test')->name('saml2_sls');

    Route::get('/metadata', function () {
        return app(Saml2Auth::class)->metadata();
    })->defaults('idpName', 'test')->name('saml2_metadata');

    Route::get('/logout', function () {
        return app(Saml2Auth::class)->logout();
    })->defaults('idpName', 'test')->name('saml2_logout');

});
*/
 
Route::pattern('idpName', 'test');
 
// Rotas SAML com os NOMES que o pacote espera
Route::match(['GET','POST'], '/saml2/{idpName}/acs', [Saml2Controller::class, 'acs'])->name('saml2_acs');
Route::match(['GET','POST'], '/saml2/{idpName}/sls', [Saml2Controller::class, 'sls'])->name('saml2_sls');
 
Route::get('/saml2/{idpName}/login', [Saml2Controller::class, 'login'])->name('saml2_login');
Route::get('/saml2/{idpName}/logout', [Saml2Controller::class, 'logout'])->name('saml2_logout');
Route::get('/saml2/{idpName}/metadata', [Saml2Controller::class, 'metadata'])->name('saml2_metadata');
 
// Atalhos
Route::get('/login', fn () => redirect('/saml2/test/login'))->name('login');
Route::get('/logout', fn () => redirect('/saml2/test/logout'))->name('logout');

/* rota teste
Route::get('/saml-debug', function () {

    
    dd(
        config('saml2.test_idp_settings.idp.entityId'),
        config('saml2.test_idp_settings.idp.singleSignOnService'),
        config('saml2.test_idp_settings.idp.singleLogoutService'),
        env('SAML2_TEST_IDP_SSO_URL'),
        env('SAML2_TEST_IDP_SLO_URL')
    );

});*/
/* rodas padroes 
Route::get('/login', function () {
    return redirect('/iam/login');
})->name('login');
*/
// LOGIN TRADICIONAL (email/senha) - para testes
Route::get('/admin/login', function () {
    return view('auth.login');
})->name('admin.login');
Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');

// Cadastro público para testes/primeiro acesso
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/user', function () {
        return redirect('/dashboard');
    })->name('user');
});

Route::post('/logout', function () {

    if (Auth::check()) {
        ActivityLogger::log(
            ActivityLog::ACTION_LOGOUT,
            'Logout realizado',
            Auth::user()
        );
    }

    Auth::logout();

    return redirect('/login');

})->name('logout');

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function (Request $request) {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->email === 'manuela.viana@fiocruz.br';

        $rascunhoIds = Situacao::where('nome', 'like', '%Rascunho%')->pluck('id');

        $filters = [
            'idioma' => $request->input('idioma'),
            'status' => $request->input('status'),
            'numero_caso' => trim((string) $request->input('numero_caso')),
            'unidade_id' => $request->input('unidade_id'),
        ];

        $baseQuery = Tecnologias_idiomas::with(['situacao', 'estagio', 'unidade'])
            ->when($filters['idioma'], fn ($query, $idioma) => $query->where('idioma', $idioma))
            ->when($filters['numero_caso'], fn ($query, $numeroCaso) => $query->where('numero_caso', 'like', '%' . $numeroCaso . '%'))
            ->when($filters['unidade_id'], fn ($query, $unidadeId) => $query->where('unidade_id', $unidadeId));

        
$statusSelecionado = (string) ($filters['status'] ?? 'todos');

$tecnologias = (clone $baseQuery)
    ->when(
        $statusSelecionado === '1',
        fn ($query) => $query->where('situacao_id', 1)
    )
    ->when(
        $statusSelecionado === '3',
        fn ($query) => $query->where('situacao_id', 3)
    )
    ->when(
        $statusSelecionado === '5',
        fn ($query) => $query->where('situacao_id', 5)
    )
    ->when(
        in_array($statusSelecionado, ['todos', '', 'null'], true),
        function ($query) use ($rascunhoIds) {
            $query->where(function ($subQuery) use ($rascunhoIds) {
                $subQuery
                    ->whereNull('situacao_id')
                    ->orWhereNotIn('situacao_id', $rascunhoIds);
            });
        }
    )
    ->orderByDesc('data_submissao')
    ->get();

        $rascunhos = (clone $baseQuery)
            ->when($filters['status'] === 'publicado', fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['status'] !== 'publicado', fn ($query) => $query->whereIn('situacao_id', $rascunhoIds))
            ->where('id_user_criador', $user->id)
            ->orderByDesc('updated_at')
            ->get();

        $tecnologiasExcluidas = $isAdmin
            ? Tecnologias_idiomas::onlyTrashed()->with(['situacao', 'estagio', 'unidade'])->orderByDesc('deleted_at')->get()
            : collect();

        $unidades = Unidade::orderBy('nome')->get();

        return view('dashboard', compact('tecnologias', 'rascunhos', 'tecnologiasExcluidas', 'filters', 'unidades'));
    })->name('dashboard');

    Route::get('/tecnologias/nova', [AddTecnologiaController::class, 'index'])->name('technology.index');
    Route::post('/tecnologias', [AddTecnologiaController::class, 'store'])->name('technology.store');
    Route::get('/tecnologias/{tecnologia}', [AddTecnologiaController::class, 'show'])->name('technology.show');
    Route::get('/tecnologias/{tecnologia}/editar', [AddTecnologiaController::class, 'edit'])->name('technology.edit');
    Route::put('/tecnologias/{tecnologia}', [AddTecnologiaController::class, 'update'])->name('technology.update');
    Route::delete('/tecnologias/{tecnologia}', [AddTecnologiaController::class, 'destroy'])->name('technology.destroy');
    Route::post('/tecnologias/{id}/restaurar', [AddTecnologiaController::class, 'restore'])->name('technology.restore');
    Route::post('/tecnologias/{id}/forcar-exclusao', [AddTecnologiaController::class, 'forceDelete'])->name('technology.forceDelete');

});

/*
|--------------------------------------------------------------------------
| ROTAS ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // =========================
        // USUÁRIOS
        // =========================
        Route::get('/usuarios', [UserController::class, 'index'])
            ->name('usuarios.index');

        Route::get('/usuarios/{user}', [UserController::class, 'show'])
            ->name('usuarios.show');

        Route::patch('/usuarios/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])
            ->name('usuarios.toggleAdmin');

        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
            ->name('usuarios.destroy');


        // =========================
        // ATIVIDADES
        // =========================
        Route::get('/atividades', [ActivityLogController::class, 'index'])
            ->name('atividades.index');

        Route::get('/usuarios/{user}/atividades', [ActivityLogController::class, 'user'])
            ->name('usuarios.atividades');

    });
Route::get(
    '/tecnologias/{tecnologia}/versao-ingles',
    [AddTecnologiaController::class, 'versaoIngles']
)->name('technology.english-version');
/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('errors.404');
});

/*
Route::redirect('/login-fiocruz',  route('saml2_login',  config('saml2_settings.idpNames')))->name('login.fiocruz');
Route::redirect('/logout-fiocruz', route('saml2_logout', config('saml2_settings.idpNames')))->name('logout.fiocruz');
*/
