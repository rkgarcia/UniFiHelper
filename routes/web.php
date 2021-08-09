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

Route::middleware(['basic.custom'])->group(function () {
    Route::get('vouchers', 'VoucherController@index');
    Route::get('vouchers/active', 'VoucherController@actives');
    Route::get('vouchers/id/{id}', 'VoucherController@byId');
    Route::get('vouchers/code/{code}', 'VoucherController@byCode');
    Route::delete('vouchers/{id}', 'VoucherController@delete');

    Route::post('vouchers/{site}/create', 'VoucherController@create');

    Route::get('guests', 'GuestController@index');
    Route::get('guests/active', 'GuestController@actives');
    Route::get('guests/expireds', 'GuestController@expireds');
    Route::get('guests/id/{id}', 'GuestController@byId');
    Route::get('guests/voucher/id/{id}', 'GuestController@byVoucherId');
    Route::get('guests/voucher/code/{code}', 'GuestController@byVoucherCode');

    Route::get('verify/login', function() {
        return response()->json([]);
    });

});