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

Route::prefix('instamojo')->middleware(['auth','student'])->group(function() {
    Route::get('/deposit-success', 'InstamojoController@depositSuccess')->name('instamojoDepositSuccess');
    Route::get('/payment-success', 'InstamojoController@paymentSuccess')->name('instamojoPaymentSuccess');
});
