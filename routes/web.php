<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\InscriptionController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Back Office — toutes les routes sont protégées par le middleware auth.
| Tout accès non authentifié est redirigé vers /login.
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/admin');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Œuvres (la photo est stockée en base : une route dédiée la sert)
    Route::get('oeuvres/{image}/photo', [ImageController::class, 'photo'])
        ->name('oeuvres.photo');
    Route::resource('oeuvres', ImageController::class)
        ->parameters(['oeuvres' => 'image']);

    // Évènements
    Route::resource('evenements', EventController::class)
        ->parameters(['evenements' => 'event']);

    // Inscriptions (consultation et suppression uniquement)
    Route::resource('inscriptions', InscriptionController::class)
        ->only(['index', 'destroy']);

    // Réservations d'œuvres
    Route::get('reservations', [ReservationController::class, 'index'])
        ->name('reservations.index');
    Route::patch('reservations/{reservation}/statut', [ReservationController::class, 'updateStatus'])
        ->name('reservations.update-status');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])
        ->name('reservations.destroy');

    // Tags
    Route::resource('tags', TagController::class)->except(['show']);

    // Utilisateurs
    Route::resource('utilisateurs', UserController::class)
        ->except(['show'])
        ->parameters(['utilisateurs' => 'utilisateur']);
    Route::patch('utilisateurs/{utilisateur}/mot-de-passe', [UserController::class, 'resetPassword'])
        ->name('utilisateurs.reset-password');
    Route::patch('utilisateurs/{utilisateur}/activation', [UserController::class, 'toggleActive'])
        ->name('utilisateurs.toggle-active');

    // Paramètres
    Route::get('parametres', [SettingsController::class, 'edit'])->name('parametres.edit');
    Route::patch('parametres/profil', [SettingsController::class, 'updateProfile'])->name('parametres.profil');
});

require __DIR__.'/auth.php';
