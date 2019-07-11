<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;

class PayController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function pay(){
        pay(time()+rand(1,10000),'手机','0.01','锤子');
        
    }

    public function payreturn(){
        echo "付款成功";
    }

}
