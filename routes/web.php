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

//Route::get('/', function () {
//    return view('welcome');
//});

//静态页面路由
Route::get('/','StaticpagesController@home')->name('home');
Route::get('/help','StaticpagesController@help')->name('help');
Route::get('/about','StaticpagesController@about')->name('about');

//非静态页面理由

Route::get('signup','UsersController@create')->name('signup');
Route::get('/users/{user}/edit','UsersController@edit')->name('users.edit');
Route::resource('users','UsersController');

Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('loginout','SessionsController@destroy')->name('logout');

//激活路由
Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');

