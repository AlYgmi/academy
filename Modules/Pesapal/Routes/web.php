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

Route::prefix('pesapal')->middleware(['auth','student'])->group(function () {
    Route::get('/', 'PesapalController@index');
    Route::get('/success', 'PesapalController@success')->name('pesapalSuccess');
});
