<?php

use Illuminate\Support\Facades\Route;
use Alimi7372\DBLogger\Http\Controllers\Api\v1\LogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */


Route::group([
    'prefix' => 'api/v1',
], function () {
    Route::get('/', [LogController::class, 'indexApi'])->name('index.api');
    Route::get('/overview', [LogController::class, 'overviewApi'])->name('overview.api');
    Route::get('/get/{id}/input', [LogController::class, 'getInput'])->name('show');
});

