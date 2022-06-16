<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
 

Route::post('/external_calls/game/bet', [App\Http\Controllers\ExtGameController::class, 'callbackBet'])->name('callbackBet');
Route::any('/external_calls/game/balance', [App\Http\Controllers\ExtGameController::class, 'callbackBalance'])->name('callbackBalance');

