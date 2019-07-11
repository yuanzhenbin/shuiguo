<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\Goods;
use app\common\model\Orders;
use app\common\model\User_address;
use app\common\model\Orders_info;

class HomeOrderController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //跳转
        $cart = session('cart');
        $data = array();
        $info = array();
        $total = '';//总计
        if ($cart) {
            foreach ($cart as $k => $v) {
                $result = Goods::where("id","=",$v['id'])->find();
                $info['id'] = $v['id'];//商品id
                $info['name'] = $result->goods_name;//商品名
                $info['pic'] = $result->pic;//商品图
                $info['price'] = $result->price;//单价
                $info['total'] = $result->total;//库存
                $info['num'] = $v['number'];//数量
                $info['sum'] = $info['price']*$info['num'];//小计
                $total += $info['sum'];//总计
                $data[] = $info;
            }
        } 
        $jifen = round($total/10);
        session('jifen',$jifen);
        $address = User_address::where("user_id","=",session('uid'))->select();
        // var_dump($data);exit;
        return view("/Orders",['data'=>$data,'total'=>$total,'address'=>$address,'jifen'=>$jifen]);
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
        $data = User_address::where("id","=",$request->post('address'))->find();
        // var_dump($data);exit;
        //拼接要加到订单表的信息
        $arr['user_name'] = $data->name;//此处的名字是收货地址的收件人，不是用户名
        $arr['address'] = $data->city;  //地址
        $arr['phone'] = $data->phone;   //此处的电话是收货地址的电话
        $arr['user_id'] = session('uid');
        $arr['total'] = $request->post('total');           //总价
        $arr['addtime'] = date("Y-m-d H:i:s",time());       //生成订单时间
        $arr['code'] = session('uid').time().rand(1,10000); //订单号
        $arr['liuyan'] = $request->post('liuyan');
        $zhifu = $request->post('zhifu');                  //支付方式

        $orders_id = Db::name('orders')->insertGetId($arr);;
        $order_name = '';
        if ($orders_id) {
            $cart = session('cart');
            $info = array();
            if ($cart) {
                foreach ($cart as $k => $v) {
                    $result = Goods::where("id","=",$v['id'])->find();
                    $info['orders_id'] = $orders_id;            //订单id
                    $info['goods_id'] = $v['id'];               //商品id
                    $info['goods_name'] = $result->goods_name;  //商品名
                    $info['num'] = $v['number'];                //数量
                    $info['price'] = $result->price;            //价格
                    $info['pic'] = $result->pic;            //价格
                    Orders_info::create($info,true);
                    $order_name = $order_name.$result->goods_name;
                }
            }else{
                return back();//购物车session为空
            }
        }else{
            return back();//订单添加失败
        }
        // var_dump($arr);exit;
        switch ($zhifu) {
            case 1:
                session('orders_id',$orders_id);
                pay($arr['code'],$order_name,'0.01','易田商城');//$arr['total']真实价格
                break;
            
            case 2:
                // echo "<script>alert('暂时不支持微信支付')</script>";
                session('orders_id',$orders_id);
                return $this->redirect("/payreturn");
                break;
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

    //支付成功
    public function payreturn(Request $request){
        //用户id
        $uid = session('uid');
        //成功结算的订单id
        $order_id = session('orders_id');
        //此订单包含的商品信息
        $goods_info = Orders_info::where("orders_id","=",$order_id)->select();
        //结算成功后把商品库存减去卖出的数量,销量加上这个数
        foreach ($goods_info as $k => $v) {
            $num = $v->num;
            $gid = $v->goods_id;
            $g_info = Goods::where("id","=",$gid)->find();
            $oldnum = $g_info->total;
            $oldsales = $g_info->sales;
            $goods['total'] = $oldnum-$num;
            $goods['sales'] = $oldsales+$num;
            Goods::update($goods,['id'=>$gid],true);
        }
        //修改支付状态
        $arr['status'] = 1;
        //获取原积分
        $ujifen = Userinfo::where("user_id","=",$uid)->find()->jifen;
        //原积分加新积分
        $jifen['jifen'] = session('jifen') + $ujifen;
        //修改积分
        Userinfo::update($jifen,['user_id'=>$uid],true);
        Orders::update($arr,['id'=>$order_id],true);//改变订单状态
        session('order_id',null);//删除订单号
        session('cart',null);//如果购买成功会清空购物车
        echo "<script>alert('购买成功，请等待发货');location='/user'</script>";
    }

}
