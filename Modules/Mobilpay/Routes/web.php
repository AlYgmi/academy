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

Route::prefix('mobilpay')->middleware(['auth', 'student'])->group(function () {
    Route::get('/return', 'MobilpayController@return')->middleware(['auth', 'student']);
});

Route::post('mobilpay/confirm/deposit', 'MobilpayController@confirmDeposit');
Route::post('mobilpay/confirm/payment', 'MobilpayController@confirmPayment');
