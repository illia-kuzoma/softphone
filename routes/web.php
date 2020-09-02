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
//Route::get('/auth', 'SoftPhone\Auth@auth');
Route::get('/auth', 'SoftPhone\Auth@getAuth');
Route::get('/test', 'SoftPhone\Auth@getTest');

Route::post('/auth', 'SoftPhone\Auth@postAuth');
Route::match(array( 'PUT', 'DELETE'),'/auth', static function () {
    return redirect('/');
});
/*Route::group([
    'middleware' => ['api', 'cors'],
    'namespace' => $this->namespace,
    'prefix' => 'api',
], function ($router) {
    //Add you routes here, for example:
    Route::apiResource('/auth','PostController');
});*/

// getCalls
Route::get('/report/missed/call/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', 'SoftPhone\ReportUnattended@getCalls');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/missed/call/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', static function () {
    return redirect('/');
});
// refreshData
Route::get('/report/missed/refresh/{token?}/{startDate?}/{period?}/{uids?}', 'SoftPhone\ReportUnattended@getFreshData');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/missed/refresh/{token?}/{startDate?}/{period?}/{uids?}', static function () {
    return redirect('/');
});
// getAll
Route::get('/report/missed/{token?}/{startDate?}/{period?}/{uids?}', 'SoftPhone\ReportUnattended@getAll');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/missed/{token?}/{startDate?}/{period?}/{uids?}', static function () {
    return redirect('/');
});

// get statuses pages
Route::get('/report/agent/status/page/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', 'SoftPhone\ReportAgentStatus@getPage');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/agent/status/page/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', static function () {
    return redirect('/');
});
// get total agent statuses in time
Route::get('/report/agent/status/total/page/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', 'SoftPhone\ReportAgentStatus@getTotalPage');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/agent/status/total/page/{startDate?}/{period?}/{departments?}/{teams?}/{uid?}/{searchWord?}/{sortField?}/{sortBy?}/{page?}', static function () {
    return redirect('/');
});
// get agent statuses in time
Route::get('/report/agent/status/{token?}/{startDate?}/{period?}/{uids?}', 'SoftPhone\ReportAgentStatus@getAll');
Route::match(array('POST', 'PUT', 'DELETE'),'/report/agent/status/{token?}/{startDate?}/{period?}/{uids?}', static function () {
    return redirect('/');
});
return redirect('/');
