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

// Auth
Route::get('/auth', 'SoftPhone\Auth@auth');
Route::match(array('POST', 'PUT', 'DELETE'),'/auth', static function () {
    return redirect(404);
});

// getCalls
Route::get('/report/missed/call/{startDate?}/{period?}/{uid?}/{searchWorld?}/{sortField?}/{sortBy?}', 'SoftPhone\ReportMissed@getCalls');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/missed/call/{startDate?}/{period?}/{uid?}/{searchWorld?}/{sortField?}/{sortBy?}', static function () {
    return redirect(404);
});

// getAll
Route::get('/report/missed/{startDate?}/{period?}', 'SoftPhone\ReportMissed@getAll');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/missed/{startDate?}/{period?}', static function () {
    return redirect(404);
});
