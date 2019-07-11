<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\User_address;
use app\common\model\District;

class HomeAddressController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        $data = User_address::where("user_id","=",session("uid"))->select();
        // var_dump($data);exit;
        return view('/user_address',["data"=>$data]);
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
        //添加用户收货地址
        $data = $request->post();
        // var_dump($data);exit;
        $data['city'] = $data['city'].','.$data['address'];
        // dd($data);
        //往data中加入 用户id
        $data['user_id'] = session('uid');
        //连接数据库 添加数据
        //判断是否添加成功
        try{
            User_address::create($data,true);
        }catch(\Exception $e){
            return $this->error('修改失败');
        }
        return $this->redirect('/user_address'); 
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
        //删除地址
        if (User_address::destroy($id)) {
            return $this->redirect('/user_address');
        }else{
            return $this->error('删除失败');
        }
    }

    public function address(Request $request)
    {
        //获取传过来的 upid 的值
        $upid = $request->get('upid');
        //连接数据库
        $list = District::where('upid','=',$upid)->select();
        //将对象遍历为数组
        foreach ($list as $key => $value) {
            $arr[$key][] = $value->name;
            $arr[$key][] = $value->id;
            $arr[$key][] = $value->level;
            $arr[$key][] = $value->upid;
            //var_dump($arr);
        }
        //var_dump($arr);
        //用js格式返回
        echo json_encode($list);
    }

    public function deladdress($id)
    {
        //删除地址 //从订单来
        if (User_address::destory($id)) {
            return $this->redirect('/Orders');
        }else{
            return back();
        }
    }
    public function deladd($id)
    {
        //删除地址 从积分订单来
        if (User_address::destroy($id)) {
            return back();
        }else{
            return back();
        }
    }

}
