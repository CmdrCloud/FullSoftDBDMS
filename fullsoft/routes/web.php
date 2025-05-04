<?php

use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\ReporteController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('ventas', [VentasController::class, 'index'])->name('ventas');
Route::get('catalogo', [CatalogController::class, 'index'])->name('catalogo');
Route::view('backup',   'backup')->name('backup');
Route::view('reportes', 'reportes')->name('reportes');

Route::get('api/vehicles/{id}', [VentasController::class, 'getVehicleDetails']);
Route::get('/reporte-ventas', [ReporteController::class, 'generarReporteVentas'])->name('reportes.ventas');

Route::get('home', fn() => redirect()->route('home'));

// Auth routes...
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected (Auth + Verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // User settings (Livewire)
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile',    Profile::class)->name('settings.profile');
    Route::get('settings/password',   Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    /*
    |--------------------------------------------------
    | Vehicles - Protected by "Encargado de Ventas" role
    |--------------------------------------------------
    */
    Route::middleware('role:Encargado de Ventas')->group(function () {
        // "Manage Vehicles" page
        Route::get('gestionar_vehiculos', [VehicleController::class, 'index'])
             ->name('gestionar_vehiculos');
        // All other vehicle CRUD
        Route::resource('vehicles', VehicleController::class)
             ->except('index');
    });

    /*
    |--------------------------------------------------
    | Users - Protected by "Encargado de Ventas" role
    |--------------------------------------------------
    */
    Route::middleware('role:Encargado de Ventas')->group(function () {
        // "Manage Users" page
        Route::get('gestionar_usuarios', [UserController::class, 'index'])
             ->name('gestionar_usuarios');
        // All other user CRUD
        Route::resource('users', UserController::class)
             ->except(['index','show']);
    });

    /*
    |--------------------------------------------------
    | API Routes for Vehicle Sales
    |--------------------------------------------------
    */
    Route::prefix('api')->group(function () {
        // Procesa la venta
        Route::post('process-sale', [VentasController::class, 'processSale']);
    });
});
