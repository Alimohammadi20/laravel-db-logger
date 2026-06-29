<?php

use Alimi7372\DBLogger\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::group([
    'prefix' => 'logs',
], function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/overview', [LogController::class, 'overview'])->name('overview');
    Route::get('/{date}/destroy', [LogController::class, 'destroy'])->name('destroy');
});

require __DIR__ . '/api/v1.php';