<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OwnerRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/**
 * Dashboard "central"
 * Redireciona para o dashboard conforme o papel do utilizador.
 * NOTA: o nome da rota (admin.dashboard) NÃO tem relação com o nome da view.
 */
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Se por algum motivo não houver user (não deveria, por causa do middleware),
    // manda para login.
    if (! $user) {
        return redirect()->route('login');
    }

    // Usa o campo "papel" (ADMIN, FUNCIONARIO, PROPRIETARIO, CLIENTE...)
    $papel = $user->papel ?? null;

    $route = match ($papel) {
        'ADMIN'        => 'admin.dashboard',
        'FUNCIONARIO'  => 'funcionario.dashboard',
        'PROPRIETARIO' => 'proprietario.dashboard',
        'CLIENTE'      => 'cliente.dashboard',
        default        => 'cliente.dashboard',
    };

    return redirect()->route($route);
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Grupos de rotas por perfil (RBAC)
 */
Route::middleware(['auth'])->group(function () {

    // =========================
    // ADMIN
    // =========================
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':ADMIN')
        ->group(function () {

            // GET /admin  -> route name: admin.dashboard
            Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('settings', \App\Http\Controllers\Admin\SettingsController::class)
                ->only(['index', 'edit', 'update']);

            // Relatórios
            Route::get('reports/reservas', [\App\Http\Controllers\Admin\ReportController::class, 'reservas'])
                ->name('reports.reservas');

            Route::get('reports/receitas', [\App\Http\Controllers\Admin\ReportController::class, 'receitas'])
                ->name('reports.receitas');

            Route::get('reports/ocupacao', [\App\Http\Controllers\Admin\ReportController::class, 'ocupacao'])
                ->name('reports.ocupacao');

            // Auditoria / Logs
            Route::get('audits', [\App\Http\Controllers\Admin\AuditController::class, 'index'])
                ->name('audits.index');
        });

    // =========================
    // FUNCIONARIO
    // =========================
    Route::prefix('funcionario')
        ->name('funcionario.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':FUNCIONARIO')
        ->group(function () {

            // GET /funcionario -> route name: funcionario.dashboard
            Route::get('/', [\App\Http\Controllers\Funcionario\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('approvals/owners', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'owners'])
                ->name('approvals.owners');

            Route::post('approvals/owners/{id}/approve', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'approveOwner'])
                ->name('approvals.approveOwner');

            Route::post('approvals/owners/{id}/reject', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'rejectOwner'])
                ->name('approvals.rejectOwner');

            Route::get('approvals/venues', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'venues'])
                ->name('approvals.venues');

            Route::post('approvals/venues/{id}/approve', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'approveVenue'])
                ->name('approvals.approveVenue');

            Route::post('approvals/venues/{id}/reject', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'rejectVenue'])
                ->name('approvals.rejectVenue');
        });

    // =========================
    // CLIENTE (solicitação para virar PROPRIETARIO)
    // =========================
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class . ':CLIENTE'])->group(function () {
        Route::get('/owner/request', [OwnerRequestController::class, 'create'])
            ->name('owner.request');

        Route::post('/owner/request', [OwnerRequestController::class, 'store'])
            ->name('owner.request.store');
    });

    // =========================
    // PROPRIETARIO
    // =========================
    Route::prefix('proprietario')
        ->name('proprietario.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':PROPRIETARIO')
        ->group(function () {

            // GET /proprietario -> route name: proprietario.dashboard
            Route::get('/', [\App\Http\Controllers\Proprietario\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('venues', \App\Http\Controllers\Proprietario\VenueController::class);
        });

    // =========================
    // CLIENTE
    // =========================
    Route::prefix('cliente')
        ->name('cliente.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':CLIENTE')
        ->group(function () {

            // GET /cliente -> route name: cliente.dashboard
            Route::get('/', [\App\Http\Controllers\Cliente\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('venues', [\App\Http\Controllers\Cliente\VenueSearchController::class, 'index'])
                ->name('venues.index');

            Route::get('venues/{venue}', [\App\Http\Controllers\Cliente\VenueSearchController::class, 'show'])
                ->name('venues.show');

            Route::post('reservations', [\App\Http\Controllers\Cliente\ReservationController::class, 'store'])
                ->name('reservations.store');

            Route::get('payments/{reservation}/create', [\App\Http\Controllers\Cliente\PaymentController::class, 'create'])
                ->name('payments.create');

            Route::post('payments/{reservation}', [\App\Http\Controllers\Cliente\PaymentController::class, 'store'])
                ->name('payments.store');
        });
});

// Auth routes (Breeze)
require __DIR__ . '/auth.php';
