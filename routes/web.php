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

Route::view('/auth', 'auth');

Route::post('/users/ajaxAuthRequest', 'UsersController@ajaxAuthRequestPost')->name('usersAuth.post');

Route::get('/', 'MainController@index')->name('main.index')->middleware('checkUser', 'loadSettings', 'removeDocFiles');

Route::get('/log/{doc_id?}', 'LogController@index')->name('log.index')->middleware('checkUser', 'loadSettings', 'removeDocFiles');
Route::post('/log', 'LogController@index')->name('log.index')->middleware('checkUser', 'loadSettings', 'removeDocFiles');

Route::get('/docs/{doctypes_id}', 'DocsController@index')->name('docs.index')->middleware('checkUser', 'loadSettings', 'removeDocFiles');
Route::post('/docs/{doctypes_id}', 'DocsController@index')->name('docs.index')->middleware('checkUser', 'loadSettings');
Route::put('/docs/{doctypes_id}/{id}', 'DocsController@update')->name('docs.update')->middleware('checkUser', 'checkRoles');
Route::get('/docs/{doctypes_id}/{id}', 'DocsController@show')->name('docs.show')->middleware('checkUser', 'removeDocFiles', 'checkRoles');
Route::get('/docs/file/{doc_id}/{file}', 'DocsController@file')->name('docs.file');

Route::fallback(function () {
    abort(404);
});
