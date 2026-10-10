<?php

declare(strict_types=1);

use App\Http\Controllers\PrescriptionFileController;
use App\Livewire\Auth\Login;
use App\Livewire\Clients\ClientList;
use App\Livewire\Clients\ClientShow;
use App\Livewire\Dashboard\DashboardOverview;
use App\Livewire\Prescriptions\PrescriptionList;
use App\Livewire\Settings\StoreSettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Logout Route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Protected Application Routes
Route::middleware('auth')->group(function () {
    // 1. Dashboard
    Route::get('/', DashboardOverview::class)->name('dashboard');

    // 2. Settings (Admin only)
    Route::get('/settings', StoreSettingsManager::class)
        ->middleware('can:manage-settings')
        ->name('settings.index');

    // 3. Client Management (Phase 3)
    Route::get('/clients', ClientList::class)->name('clients.index');
    Route::get('/clients/{client}', ClientShow::class)->name('clients.show');

    // 4. Prescription & Refraction Management (Phase 4)
    Route::get('/prescriptions', PrescriptionList::class)->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}/file', [PrescriptionFileController::class, 'show'])->name('prescriptions.file');

    Route::get('/orders', function () {
        return view('placeholder', [
            'title' => 'Commandes & Atelier',
            'description' => 'Le configurateur de commandes (montures, verres, acomptes) sera implémenté dans la Phase 5.',
        ]);
    })->name('orders.index');

    Route::get('/payments', function () {
        return view('placeholder', [
            'title' => 'Paiements & Règlements',
            'description' => 'Le suivi des encaissements et des soldes sera implémenté dans la Phase 6.',
        ]);
    })->name('payments.index');

    Route::get('/documents', function () {
        return view('placeholder', [
            'title' => 'Factures & Bons de Commande',
            'description' => 'La génération des documents PDF aux normes marocaines sera implémentée dans la Phase 7.',
        ]);
    })->name('documents.index');

    Route::get('/reminders', function () {
        return view('placeholder', [
            'title' => 'Rappels & Suivi Patients',
            'description' => 'Le moteur de rappels (retrait lunettes, relance solde, contrôle de la vue) sera implémenté dans la Phase 8.',
        ]);
    })->name('reminders.index');

    Route::get('/analytics', function () {
        return view('placeholder', [
            'title' => 'Analytiques & Chiffre d\'affaires',
            'description' => 'Les graphiques et rapports financiers avancés seront implémentés dans la Phase 9.',
        ]);
    })->name('analytics.index');
});
