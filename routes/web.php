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

//resource路由
Route::resource('users','UsersController');
Route::resource('statuses','StatusesController',['only'=>['store','destroy']]);

Route::get('login','SessionsController@create')->name('login');
Route::post('login','SessionsController@store')->name('login');
Route::delete('loginout','SessionsController@destroy')->name('logout');

//激活邮件路由
Route::get('signup/confirm/{token}','UsersController@confirmEmail')->name('confirm_email');


//忘记密码路由
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


//粉丝路由
Route::get('users/{user}/followings','UsersController@followings')->name('users.followings');
Route::get('users/{user}/followers','UsersController@followers')->name('users.followers');

//关注用户和取消用户
Route::post('/users/followers/{user}','FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');

