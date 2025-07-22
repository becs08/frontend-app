<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return redirect()->route('offres.index');
});

// Debug route for performance testing
Route::get('/debug/offres-performance', function () {
    $startTime = microtime(true);
    
    $apiClient = app(\App\Services\ApiClient::class);
    
    // Test API endpoints individually
    $results = [];
    
    // Test getOffres
    $offresStart = microtime(true);
    $offresResponse = $apiClient->getOffres([]);
    $offresTime = microtime(true) - $offresStart;
    $results['getOffres'] = [
        'time' => $offresTime,
        'status' => $offresResponse->status(),
        'successful' => $offresResponse->successful()
    ];
    
    // Test getCategories
    $categoriesStart = microtime(true);
    $categoriesResponse = $apiClient->getCategories();
    $categoriesTime = microtime(true) - $categoriesStart;
    $results['getCategories'] = [
        'time' => $categoriesTime,
        'status' => $categoriesResponse->status(),
        'successful' => $categoriesResponse->successful()
    ];
    
    $totalTime = microtime(true) - $startTime;
    
    return response()->json([
        'total_time' => $totalTime,
        'api_base_url' => config('app.api_base_url'),
        'api_timeout' => config('app.api_timeout'),
        'results' => $results
    ]);
});

// Routes publiques
Route::get('/offres', [OffreController::class, 'indexOptimized'])->name('offres.index');
Route::get('/offres/{id}', [OffreController::class, 'show'])->name('offres.show');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes protégées
Route::middleware(['frontend.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Offres
    Route::get('/offres-create', [OffreController::class, 'create'])->name('offres.create');
    Route::post('/offres', [OffreController::class, 'store'])->name('offres.store');
    Route::get('/offres/{id}/edit', [OffreController::class, 'edit'])->name('offres.edit');
    Route::put('/offres/{id}', [OffreController::class, 'update'])->name('offres.update');
    Route::delete('/offres/{id}', [OffreController::class, 'destroy'])->name('offres.destroy');
    Route::get('/mes-offres', [OffreController::class, 'mesOffres'])->name('offres.mes-offres');

    // Demandes
    Route::post('/offres/{id}/demandes', [DemandeController::class, 'store'])->name('demandes.store');
    Route::put('/demandes/{id}', [DemandeController::class, 'update'])->name('demandes.update');
    Route::get('/mes-demandes', [DemandeController::class, 'mesDemandes'])->name('demandes.mes-demandes');
    Route::get('/demandes-recues', [DemandeController::class, 'demandesRecues'])->name('demandes.demandes-recues');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
