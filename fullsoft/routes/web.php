<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Auth;

// Public pages
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('catalogo', [CatalogController::class, 'index'])->name('catalogo');
Route::get('/reporte-ventas', [ReporteController::class, 'generarReporteVentas'])->name('reportes.ventas');
Route::view('reportes', 'reportes')->name('reportes');

// Vehicle details API (public)
Route::get('api/vehicles/{id}', [VentasController::class, 'getVehicleDetails']);

// Authentication routes (login, register, logout via POST)
require __DIR__.'/auth.php';

// Optional: allow GET logout for convenience
Route::middleware('auth')->get('logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout.get');

// Protected routes (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Sales: listing + storing
    Route::get('ventas', [VentasController::class, 'index'])->name('ventas');
    Route::post('ventas', [VentasController::class, 'store'])->name('ventas.store');

    // API route for AJAX sale processing
    Route::prefix('api')->group(function () {
        Route::post('process-sale', [VentasController::class, 'store'])->name('api.processSale');
    });

    // Backup management (Encargado de Ventas)
    Route::middleware('role:Encargado de Ventas')->prefix('backup')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('backup');
        Route::post('/', [BackupController::class, 'create'])->name('backup.run');
        Route::get('download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    });

    // User settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Commission management
    Route::get('/my-commission', [UserController::class, 'myCommission'])->name('user.commission');
    Route::view('/commissions', 'commissions')->name('commissions');

    // Vehicle management (Encargado de Ventas)
    Route::middleware('role:Encargado de Ventas')->group(function () {
        Route::get('gestionar_vehiculos', [VehicleController::class, 'index'])->name('gestionar_vehiculos');
        Route::resource('vehicles', VehicleController::class)->except('index');
    });

    // User management (Encargado de Ventas)
    Route::middleware('role:Encargado de Ventas')->group(function () {
        Route::get('gestionar_usuarios', [UserController::class, 'index'])->name('gestionar_usuarios');
        Route::resource('users', UserController::class)->except(['index', 'show']);
        Route::get('/user-commissions', [UserController::class, 'viewCommissions'])->name('admin.commissions');
        Route::post('/reset-commission/{id}', [UserController::class, 'resetCommission'])->name('user.reset-commission');
    });

    // Report exporting
    Route::get('/reporte-ventas/exportar', [ReporteController::class, 'exportarReporte'])->name('reportes.exportar');
});
