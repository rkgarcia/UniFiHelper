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

Route::get('/', function () {
    return view('welcome');
});

Route::get('vouchers', 'VoucherController@index');
Route::get('vouchers/active', 'VoucherController@actives');
Route::get('vouchers/id/{id}', 'VoucherController@byId');
Route::get('vouchers/code/{code}', 'VoucherController@byCode');