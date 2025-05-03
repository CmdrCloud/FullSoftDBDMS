<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('ventas', fn() => view('ventas'))->name('ventas');
Route::get('backup', fn() => view('backup'))->name('backup');
Route::get('reportes', fn() => view('reportes'))->name('reportes');

/*
|--------------------------------------------------------------------------
| Protected (Auth + Verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // User Settings (Livewire)
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Vehicle Management
    // This is your “Gestionar Vehículos” page: it calls index() and passes $vehicles
    Route::get('gestionar_vehiculos', [VehicleController::class, 'index'])
         ->name('gestionar_vehiculos');

    // All other vehicle CRUD routes
    Route::resource('vehicles', VehicleController::class)
         // if you don’t want the default index route to clash, you can exclude it:
         ->except(['index']);
});

/*
|--------------------------------------------------------------------------
| Auth (login/logout/etc)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
