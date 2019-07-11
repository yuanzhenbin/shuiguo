<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');
//----------------------前台路由---------------------------
//前台首页
Route::rule('/', 'home/HomeController/index');
Route::get("/index","home/HomeController/index");

//前台登录 带控制器写法
Route::get("/login","home/HomeController/login");
//前台执行登录
Route::resource("dologin","home/HomeLoginController");
//前台注册
Route::resource("registered","home/RegisterController");
//注册所需验证码
Route::get('/code',"home/RegisterController/code");
//激活
Route::get('/activtion',"home/RegisterController/activtion");
// 忘记密码
Route::resource("forget","home/ForgetController");
//重置密码
Route::get("/reset","home/ForgetController/reset");
//执行重置密码
Route::post("/doreset","home/ForgetController/doreset");
//注册二 调用短信接口
Route::resource("homeregisters","home/RegistersController");
Route::get("/checkphone","home/RegistersController/checkphone");
Route::get("/sendphone","home/RegistersController/sendphone");
Route::get("/checkcode","home/RegistersController/checkcode");
//所有果蔬页
Route::get("/Products","home/HomeController/Products");
//活动专区（积分商城）
Route::get("/integral","home/HomeController/integral");
//商品一级列表
Route::get("/Products_list/:id","home/HomeController/Products_list");
//商品二级列表
Route::get("/Products_lists/:id","home/HomeController/Products_lists");
//商品详情
Route::get("/Product_detailed/:id","home/HomeController/Product_detailed");
//申请友情链接
Route::resource("weblink","home/HomeLinkController");

//搜索
Route::get('/search','home/HomeController/search');

//中间件判断是否登录
Route::group([],function(){
	//收藏商品
	Route::get("/collection/:id","home/HomeController/collection");
	//我的收藏
	Route::get("/user_Collect","home/HomeController/user_collection");
	//删除收藏
	Route::get("/del_collection/:id","home/HomeController/del_collection");
	//个人中心
	Route::get("/user","home/HomeController/user");
	//个人资料
	Route::get("/user_info","home/HomeController/user_info");
	//修改密码
	Route::get("/user_Password","home/HomeController/user_Password");
	//历史订单
	Route::get("/user_Orders","home/HomeController/user_Orders");
	//我的积分
	Route::get("/user_integral","home/HomeController/user_integral");
	//前端用户修改个人信息
	Route::resource("homeuserinfo","home/HomeUserController");
	//前端用户修改密码
	Route::resource("homepassword","home/HomePasswordController");
	//用户收货地址
	Route::resource("user_address","home/HomeAddressController");
	Route::get("/address","home/HomeAddressController/address");
	Route::post("/saveaddress","home/HomeAddressController/saveaddress");
	//在订单页的删除地址
	Route::get("/deladdress/:id","home/HomeAddressController/deladdress");
	Route::get("/deladd/:id","home/HomeAddressController/deladd");
	//购物车
	Route::resource("shopping_cart","home/ShopCartController");
	//购物数量加减
	Route::get("/jian/:id","home/ShopCartController/jian");
	Route::get("/jia/:id","home/ShopCartController/jia");
	//确认订单
	Route::resource("Orders","home/HomeOrderController");
	// //支付宝接口调用
	// Route::resource("pay","home/PayController/pay");
	// //通知给客户端的界面
	Route::get("/payreturn","home/HomeOrderController/payreturn");
	//积分订单
	Route::resource("jifenorders","home/JifenOrdersController");

	//我的评论
	Route::get("/user_pinglun","home/HomeController/user_pinglun");
	//添加评论
	Route::post("/comment",'home/HomeController/comment');
	//保存评论
	Route::post("/comments",'home/HomeController/comments');
})->middleware('Homelogin');

//----------------------后台路由---------------------------
//后台首页admin
//跳转后台登录页
Route::rule('/adminlogin', 'admin/LoginController/login');
//后台登录
Route::resource("admindologin","admin/LoginController");
Route::group([],function(){
	//后台管理员模块
	Route::resource('admin','admin/AdminController');
	Route::rule('/admin/role/:id','admin/AdminController/role');
	Route::rule('/dorole','admin/AdminController/dorole');
	//角色模块
	Route::resource('role','admin/RoleController');
	//用户模块
	Route::resource('adminuser','admin/UserController');
	//用户详细信息
	Route::resource('userinfo','admin/UserInfoController');
	Route::rule('/userinfo/address/:id','admin/UserInfoController/address');
	Route::rule('/userinfo/collection/:id','admin/UserInfoController/collection');
	//分类模块
	Route::resource('type','admin/TypeController');
	//商品模块
	Route::resource('goods','admin/GoodsController');
	//订单模块
	Route::resource('adminorders','admin/OrdersController');
	//积分订单模块
	Route::resource('adminjifen','admin/JifenOrdersController');
	//评论模块
	Route::resource('message','admin/MessageController');
	//积分模块
	Route::resource('jifen','admin/JifenController');
	//友情链接模块
	Route::resource('links','admin/LinkController');
	//轮播图模块
	Route::resource('carouse','admin/CarouseController');
})->middleware('Adminlogin');