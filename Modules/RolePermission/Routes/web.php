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

use Illuminate\Support\Facades\Route;

Route::prefix('role-permission')->middleware(['auth', 'admin'])->group(function () {
    Route::name('permission.')->group(function () {
        Route::resource('roles', 'RoleController')->middleware('RoutePermissionCheck:permission.permissions.store');
        Route::get('/roles-student', 'RoleController@studentIndex')->name('student-roles')->middleware('RoutePermissionCheck:permission.permissions.store');
        Route::resource('permissions', 'PermissionController')->middleware('RoutePermissionCheck:permission.permissions.store');
    });
});
