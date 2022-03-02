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


Route::prefix('midtrans')->middleware(['auth','student'])->group(function() {
    Route::get('/callback-success', 'MidtransController@paymentSuccess')->name('midtransPaymentSuccess');
    Route::get('/callback-pending', 'MidtransController@paymentPending')->name('midtransPaymentPending');
    Route::get('/callback-failed', 'MidtransController@paymentFailed')->name('midtransPaymentfailed');
});
