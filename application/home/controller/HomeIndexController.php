<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Pic;

class HomeIndexController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
     public function index()
    {
        //
        $pic = Pic::select();
        return view("Web.index",["pic"=>$pic]);
    }
}
