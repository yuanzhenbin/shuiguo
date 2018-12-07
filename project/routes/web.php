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
Route::get("/",function(){
	return view("Web.index");
});
//设置/和/index都跳转首页
Route::get("/index",function(){
	return view("Web.index");
});

//前台登录 带控制器写法
Route::get("/login","Web\WebController@login");

//前台注册
Route::get("/registered","Web\WebController@registered");

//收藏
Route::get("/user_Collect","Web\WebController@user_Collect");

//购物车
Route::get("/shopping_cart","Web\WebController@shopping_cart");