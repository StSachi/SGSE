<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Grupos de rotas por perfil (RBAC)
Route::middleware(['auth'])->group(function () {
    // Admin: acesso total, configurações e relatórios
    Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':ADMIN')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('settings', \App\Http\Controllers\Admin\SettingsController::class)->only(['index', 'edit', 'update']);
        // Rotas de auditoria e relatórios serão adicionadas na PARTE 8
    });

    // Funcionario: aprovar proprietários e salões
    Route::prefix('funcionario')->name('funcionario.')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':FUNCIONARIO')->group(function () {
        Route::get('/', [\App\Http\Controllers\Funcionario\DashboardController::class, 'index'])->name('dashboard');
        // Rotas para aprovações de proprietários e salões
        Route::get('approvals/owners', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'owners'])->name('approvals.owners');
        Route::post('approvals/owners/{id}/approve', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'approveOwner'])->name('approvals.approveOwner');
        Route::post('approvals/owners/{id}/reject', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'rejectOwner'])->name('approvals.rejectOwner');

        Route::get('approvals/venues', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'venues'])->name('approvals.venues');
        Route::post('approvals/venues/{id}/approve', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'approveVenue'])->name('approvals.approveVenue');
        Route::post('approvals/venues/{id}/reject', [\App\Http\Controllers\Funcionario\ApprovalController::class, 'rejectVenue'])->name('approvals.rejectVenue');
    });

    // Proprietario: CRUD de salões
    Route::prefix('proprietario')->name('proprietario.')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':PROPRIETARIO')->group(function () {
        Route::get('/', [\App\Http\Controllers\Proprietario\DashboardController::class, 'index'])->name('dashboard');
        // CRUD de venues para proprietario
        Route::resource('venues', \App\Http\Controllers\Proprietario\VenueController::class);
    });

    // Cliente: pesquisa e reservas
    Route::prefix('cliente')->name('cliente.')->middleware(\App\Http\Middleware\RoleMiddleware::class . ':CLIENTE')->group(function () {
        Route::get('/', [\App\Http\Controllers\Cliente\DashboardController::class, 'index'])->name('dashboard');
        // Pesquisa e reservas
        Route::get('venues', [\App\Http\Controllers\Cliente\VenueSearchController::class, 'index'])->name('venues.index');
        Route::get('venues/{venue}', [\App\Http\Controllers\Cliente\VenueSearchController::class, 'show'])->name('venues.show');

        // Reservas
        Route::post('reservations', [\App\Http\Controllers\Cliente\ReservationController::class, 'store'])->name('reservations.store');
        // Pagamentos (simulados)
        Route::get('payments/{reservation}/create', [\App\Http\Controllers\Cliente\PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments/{reservation}', [\App\Http\Controllers\Cliente\PaymentController::class, 'store'])->name('payments.store');
    });
});

