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


//通过在路由后面链式调用 name 方法来为路由指定名称
Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');

Route::resource('users', 'UsersController'); //遵从 RESTful 架构为用户资源生成路由
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');

Route::get('login', 'SessionsController@create')->name('login'); //显示登录页面
Route::post('login', 'SessionsController@store')->name('login'); //创建新会话（登录）
Route::delete('logout', 'SessionsController@destroy')->name('logout'); //销毁会话（退出登录