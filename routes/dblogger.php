<?php


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

use Alimi7372\DBLogger\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;


Route::get('logs', [LogController::class, 'index'])->name('index');
Route::get('logs/get/{id}/input', [LogController::class, 'getInput'])->name('show');
Route::get('api/logs/api', [LogController::class, 'indexApi'])->name('index.api');
Route::get('logs/overview', [LogController::class, 'overview'])->name('overview');
Route::get('logs/{date}/destroy', [LogController::class, 'destroy'])->name('destroy');
