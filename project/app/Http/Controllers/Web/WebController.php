<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebController extends Controller
{
    //前台登录
    public function login(){
    	return view("Web.Login");
    }

    //前台注册
    public function registered(){
    	return view("Web.registered");
    }

    //收藏夹
    public function user_Collect(){
    	return view("Web.user_Collect");
    }

    //购物车·
    public function shopping_cart(){
    	return view("Web.shopping_cart");
    }
}
