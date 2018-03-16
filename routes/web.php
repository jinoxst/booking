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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::prefix('web')->group(function () {
//     Route::get('test', function () {
//         return 'web/test';
//     });
// });

Route::get('/', 'WebController@index');
Route::match(['get', 'post'],'calendar', 'WebController@calendar');
Route::match(['get', 'post'],'zones', 'WebController@zones');
Route::match(['get', 'post'],'time', 'WebController@time');
Route::match(['get', 'post'],'userinfo', 'WebController@userinfo');
Route::match(['get', 'post'],'userinfo2', 'WebController@userinfo2');
Route::match(['get', 'post'],'done', 'WebController@done');