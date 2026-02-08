<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OwnerRequestController;
use App\Http\Controllers\SolicitacaoOwnerController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicVenueController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\FuncionarioController;

use App\Http\Controllers\Funcionario\DashboardController as FuncionarioDashboardController;
use App\Http\Controllers\Funcionario\ApprovalController;

use App\Http\Controllers\Proprietario\DashboardController as ProprietarioDashboardController;
use App\Http\Controllers\Proprietario\VenueController as ProprietarioVenueController;

use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Cliente\VenueSearchController;
use App\Http\Controllers\Cliente\ReservationController;
use App\Http\Controllers\Cliente\PaymentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/venues/{venue}', [PublicVenueController::class, 'show'])
    ->name('public.venues.show');

Route::get('/owner/request', [OwnerRequestController::class, 'create'])
    ->name('owner.request');

Route::post('/owner/request', [OwnerRequestController::class, 'store'])
    ->name('owner.request.store');

Route::get('/owner/request/sent', [OwnerRequestController::class, 'sent'])
    ->name('owner.request.sent');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    $papel = $user->papel ?? null;

    return redirect()->route(match ($papel) {
        'ADMIN'        => 'admin.dashboard',
        'FUNCIONARIO'  => 'funcionario.dashboard',
        'PROPRIETARIO' => 'proprietario.dashboard',
        'CLIENTE'      => 'cliente.dashboard',
        default        => 'cliente.dashboard',
    });
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':ADMIN')
        ->group(function () {

            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::resource('settings', SettingsController::class)
                ->only(['index', 'edit', 'update']);

            Route::get('reports/reservas', [ReportController::class, 'reservas'])->name('reports.reservas');
            Route::get('reports/receitas', [ReportController::class, 'receitas'])->name('reports.receitas');
            Route::get('reports/ocupacao', [ReportController::class, 'ocupacao'])->name('reports.ocupacao');

            Route::get('audits', [AuditController::class, 'index'])->name('audits.index');

            Route::patch('funcionarios/{funcionario}/toggle', [FuncionarioController::class, 'toggleAtivo'])
                ->name('funcionarios.toggle');

            Route::resource('funcionarios', FuncionarioController::class);
        });

    Route::prefix('funcionario')
        ->name('funcionario.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':FUNCIONARIO')
        ->group(function () {

            Route::get('/', [FuncionarioDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('solicitacoes-owners', [SolicitacaoOwnerController::class, 'index'])
                ->name('solicitacoes_owners.index');

            Route::post('solicitacoes-owners/{solicitacao}/aprovar', [SolicitacaoOwnerController::class, 'aprovar'])
                ->name('solicitacoes_owners.aprovar');

            Route::post('solicitacoes-owners/{solicitacao}/rejeitar', [SolicitacaoOwnerController::class, 'rejeitar'])
                ->name('solicitacoes_owners.rejeitar');

            Route::get('approvals/owners', [ApprovalController::class, 'owners'])
                ->name('approvals.owners');

            Route::post('approvals/owners/{id}/approve', [ApprovalController::class, 'approveOwner'])
                ->name('approvals.approveOwner');

            Route::post('approvals/owners/{id}/reject', [ApprovalController::class, 'rejectOwner'])
                ->name('approvals.rejectOwner');

            Route::get('approvals/venues', [ApprovalController::class, 'venues'])
                ->name('approvals.venues');

            Route::post('approvals/venues/{id}/approve', [ApprovalController::class, 'approveVenue'])
                ->name('approvals.approveVenue');

            Route::post('approvals/venues/{id}/reject', [ApprovalController::class, 'rejectVenue'])
                ->name('approvals.rejectVenue');
        });

    Route::prefix('proprietario')
        ->name('proprietario.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':PROPRIETARIO')
        ->group(function () {

            Route::get('/', [ProprietarioDashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('venues', ProprietarioVenueController::class);
        });

    Route::prefix('cliente')
        ->name('cliente.')
        ->middleware(\App\Http\Middleware\RoleMiddleware::class . ':CLIENTE')
        ->group(function () {

            Route::get('/', [ClienteDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('venues', [VenueSearchController::class, 'index'])
                ->name('venues.index');

            Route::get('venues/{venue}', [VenueSearchController::class, 'show'])
                ->name('venues.show');

            Route::post('reservations', [ReservationController::class, 'store'])
                ->name('reservations.store');

            Route::get('payments/{reservation}/create', [PaymentController::class, 'create'])
                ->name('payments.create');

            Route::post('payments/{reservation}', [PaymentController::class, 'store'])
                ->name('payments.store');
        });
});

Route::get('/logout', function () {
    return redirect()->route('home')
        ->with('error', 'Ação inválida. Para terminar a sessão, use o botão "Sair".');
});

require __DIR__ . '/auth.php';
