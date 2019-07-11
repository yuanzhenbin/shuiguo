<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\User_address;
use app\common\model\User_collection;
use app\common\model\Goods;

class UserInfoController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        $tiaojian = [];
        // var_dump($request->get('keywords'));exit;
        $keywords = '';
        if (!empty($request->get('keywords'))) {
            $tiaojian[] = ['name','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = Userinfo::where($tiaojian)->paginate(10)->appends($_GET);
        // var_dump($user);exit;
        return view("userinfo/index",["data"=>$data,"keywords"=>$keywords]);
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
        //删除
    }

    //会员收藏
    public function collection($id){
        //一对多，所以不能用first()
        $data = User_collection::where("user_id","=",$id)->select();
        // dd($data);
        // $data如果有值就会执行，没值就自动跳过下面一步
        foreach($data as $k=>$v){
            //把会员每一个收藏，根据id在对应表查出的信息，加到数据中
            $data[$k]->username = Userinfo::where("user_id","=",$v->user_id)->find()->name; 
            $data[$k]->goods = Goods::where("id","=",$v->goods_id)->find()->goods_name;     
        }
        
        return view("userinfo/collection",["data"=>$data]);
    }

    //会员收货地址
    public function address($id){
        $data = User_address::where("user_id","=",$id)->select();
        foreach($data as $k=>$v){
            //把会员每一个地址，根据id在对应表查出的信息，加到数据中
            if ($v) {
                $data[$k]->username = Userinfo::where("user_id","=",$v->user_id)->find()->name; 
            }else{
                $data[$k]->username = '';
            }     
        }

        return view("userinfo/address",["data"=>$data]);
    }
}
