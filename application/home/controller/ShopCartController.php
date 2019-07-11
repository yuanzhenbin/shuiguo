<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Goods;

class ShopCartController extends Controller
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
            // var_dump($cart);exit;
            foreach ($cart as $k => $v) {
                $result = Goods::where("id","=",$v['id'])->find();
                $info['id'] = $v['id'];//商品id
                $info['name'] = $result->goods_name;//商品名
                $info['pic'] = $result->pic;//商品图
                $info['price'] = $result->price;//单价
                $info['total'] = $result->total;//库存
                $info['num'] = $v['number'];//数量
                $info['describe'] = $result->describe;//数量
                $info['sum'] = $info['price']*$info['num'];//小计
                $total += $info['sum'];//总计
                $data[] = $info;
            }
        } 
        // dd($data);
        return view("/shopping_cart",['data'=>$data,'total'=>$total]);
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
        //加入购物车
        $data=$request->post();
        $arr = array();
        //判断购物车里有没有当前购买的数据 
        if(!$this->checkExists($data['id'])){
            if (session('cart')) {
                $sess = session('cart');
                foreach ($sess as $k => $v) {
                    $arr[] = $v;
                }
                $arr[] = $data;
                session('cart',null);
                session('cart',$arr);
            }else{
                $arr[] = $data;
                session('cart',$arr);
            }
        } 
        return $this->redirect("/shopping_cart");  
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
        $data = session('cart');
        foreach($data as $key=>$value){
            if($value['id'] == $id){
                unset($data[$key]);
            }
        }
        //重新赋值
        session('cart',$data);
        return redirect("/shopping_cart");
    }

    //购物车去重 $id 购买商品数据id
    public function checkExists($id){
        //获取购物车数据
        $goods=session('cart');
        // var_dump($goods);exit;
        //判断
        //购物车没有数据
        if(empty($goods)){
            return false;
        }
        //遍历
        foreach($goods as $key=>$value){
            //判断 购物车里有当前要购买的商品数据
            if($value['id']==$id){
                return true;
            }
        }
    }

    //减
    public function jian($id){
        $data = session('cart');
        foreach($data as $key=>$value){
            //如果商品id符合就执行减
            if($value['id'] == $id){
                //数量减一
                $num = $value['number']-1;
                $data[$key]['number'] = $num;
                if($data[$key]['number']<1){
                    $data[$key]['number'] = 1;
                }
            }
        }
        //重新赋值
        session('cart',$data);
        return $this->redirect("/shopping_cart");
    }

    //加
    public function jia($id){
        $data = session('cart');
        foreach($data as $key=>$value){
            //如果商品id符合就执行减
            if($value['id'] == $id){
                //数量加一
                $num = $value['number']+1;
                $data[$key]['number'] = $num;
                $info = Goods::where("id",'=',$id)->find();
                //判断数量不能超过库存
                if($data[$key]['number']>$info->total){
                    $data[$key]['number'] = $info->total;
                }
            }
        }
        //重新赋值
        session('cart',$data);
        return $this->redirect("/shopping_cart");
    }

}
