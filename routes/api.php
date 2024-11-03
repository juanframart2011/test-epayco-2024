<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


#Client
Route::prefix('client')->name('client.')->group(function(){

    Route::post('register', [UserController::class, 'register'])->name('register');
});

#Payment
Route::prefix('payment')->name('payment.')->group(function(){

    Route::post('pay', [PaymentController::class, 'pay'])->name('pay');
});

#Wallet
Route::prefix('wallet')->name('wallet.')->group(function(){

    Route::post('balance', [WalletController::class, 'balance'])->name('balance');
    Route::post('recharge', [WalletController::class, 'recharge'])->name('recharge');
});