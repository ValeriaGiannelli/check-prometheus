<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LogDisplayController;

//esempio di un mock dell'API in attesa di quelle reali poi nei controller indirizzo a queste che creo
Route::prefix('mock-api')->group(function () {
    Route::get('/clienti', function () {
        return response()->json([
            ['id' => 1, 'nome' => 'Mario Rossi', 'email' => 'mario@example.com'],
            ['id' => 2, 'nome' => 'Lucia Bianchi', 'email' => 'lucia@example.com'],
        ]);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('home');

    Route::get('/servizi', [PageController::class, 'services'])->name('services');

    Route::get('/sql-metrics', [PageController::class, 'sqlServerMetrics'] )->name('sql_metrics');

    Route::get('/customer-metrics', [PageController::class, 'customerMetrics'] )->name('customer.metrics');

    Route::get('/customer/{customer}/{instance}/{type}', [PageController::class, 'customerDetail'] )->name('customer.detail');


    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/{ip}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}/{ip}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}/{ip}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/version', [PageController::class, 'getVersion'])->name('info_version');
    
    Route::get('/logs', [LogDisplayController::class, 'index'])->name('logs.index');
    Route::post('/logs/ignore', [LogDisplayController::class, 'ignore'])->name('logs.ignore');
});


Auth::routes(['register' => false]);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
