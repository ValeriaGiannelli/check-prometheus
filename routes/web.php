<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//esempio di un mock dell'API in attesa di quelle reali poi nei controller indirizzo a queste che creo
Route::prefix('mock-api')->group(function () {
    Route::get('/clienti', function () {
        return response()->json([
            ['id' => 1, 'nome' => 'Mario Rossi', 'email' => 'mario@example.com'],
            ['id' => 2, 'nome' => 'Lucia Bianchi', 'email' => 'lucia@example.com'],
        ]);
    });
});

Route::get('/', [PageController::class, 'index'])->name('home');

Route::get('/chi-siamo', [PageController::class, 'about'])->name('about');

Route::get('/contatti', [PageController::class, 'contacts'] )->name('contacts');
