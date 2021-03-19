<?php

use Illuminate\Http\Request;

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


Route::get('log/detail','ApiController@serverLog');
Route::get('restart','ApiController@restartServer');
Route::get('getRam','ApiController@getRam');
Route::get('getProcess','ApiController@getProcess');
Route::get('getSystemInfo','ApiController@getSystemInfo');
Route::get('getServerLog','ApiController@getServerLog');
Route::get('userLog','ApiController@userLog');
