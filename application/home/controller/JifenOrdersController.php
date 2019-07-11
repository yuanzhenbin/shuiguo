<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\User_address;
use app\common\model\Goods_jifen;
use app\common\model\Orders_jifen;

class JifenOrdersController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 
        $uid = session('uid');
        $id = $request->post('id');
        $info = array();

        // echo $id;
        $goods = Goods_jifen::where("id","=",$id)->find();
        $oldjifen = Userinfo::where("user_id","=",$uid)->find()->jifen;
        $jifen = $goods->jifen_price;
        $newjifen = $oldjifen-$jifen;

        if ($newjifen >= 0) {//积分足够
            $address = User_address::where("id","=",$request->post('address'))->find();
            //拼接要加到订单表的信息
            $info['user_name'] = $address->name;//此处的名字是收货地址的收件人，不是用户名
            $info['address'] = $address->city;  //地址
            $info['phone'] = $address->phone;   //此处的电话是收货地址的电话
            $arr['jifen'] = $newjifen;
            Userinfo::update($arr,['user_id'=>$uid],true);
            $info['code'] = '123'.$uid.time().rand(1,10000);//订单号
            $info['user_id'] = $uid;
            $info['jifen_name'] = $goods->jifen_name;       //积分商品名
            $info['total'] = $jifen;                        //消耗积分
            $info['addtime'] = date("Y-m-d H:i:s",time());; //添加时间
            Orders_jifen::create($info,true);
            echo "<script>alert('购买成功，可在 积分商品订单 或 我的积分 查看');location='/integral'</script>";
        }else{
            echo "<script>alert('购买失败，您的积分不足');location='/integral'</script>";
        }   
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $address = User_address::where("user_id","=",session('uid'))->select();
        $data = Goods_jifen::where("id","=",$id)->find();
        return view("/jifen_cart",['data'=>$data,'address'=>$address]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

}
