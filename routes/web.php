<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Enode OAuth routes
    Route::get('enode/connect', [App\Http\Controllers\EnodeController::class, 'redirect'])->name('enode.connect');
    Route::get('enode/callback', [App\Http\Controllers\EnodeController::class, 'callback'])->name('enode.callback');
    Route::post('enode/disconnect', [App\Http\Controllers\EnodeController::class, 'disconnect'])->name('enode.disconnect');

    // Vehicle routes
    Route::get('vehicles/{vehicle}', App\Livewire\Vehicles\Detail::class)->name('vehicles.detail');

    // Admin routes
    Route::get('admin/vehicles', App\Livewire\Admin\AllVehicles::class)->name('admin.vehicles');
});

require __DIR__.'/auth.php';
