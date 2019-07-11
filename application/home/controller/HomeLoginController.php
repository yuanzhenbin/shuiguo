<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\User_collection;

class HomeLoginController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //退出
        //删除session信息
        session("user_name",null);
        session("uid",null);
        session("pic",null);
        session("shoucang",null);
        session("cart",null);
        return view("/Login");
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
        //添加会员session
        $password = $request->post('password');
        $username = $request->post('username');
        $data = User::where("username","=",$username)->find();
        // var_dump($data);exit;
        if ($data) {
            if (md5($password) == $data->password) {
                if ($data->status == 1) {
                    //查询用户详情
                    $userinfo = Userinfo::where("user_id","=",$data->id)->find();
                    //查询用户收藏
                    $collection = User_collection::where("user_id","=",$data->id)->select();
                    //定义收藏空数组
                    $shoucang = array();
                    // 把用户收藏的商品的id加到收藏数组
                    foreach ($collection as $k => $v) {
                        $shoucang[$k] = $v->goods_id;
                    }
                    //把用户的名字加到session,以通过中间件验证
                    session('user_name',$userinfo->name);
                    //把用户id加到session方便查询
                    session('uid',$userinfo->user_id);
                    //把用户头像加到session方便查询
                    session('pic',$userinfo->pic);
                    //把用户收藏的商品的id加到session方便查询
                    session('shoucang',$shoucang);
                    return $this->redirect("/user");
                }else{
                    return $this->error('您的账号尚未激活');
                }
            }else{
                return $this->error('账号或密码错误');;//就是密码错误
            }  
        }else{
            //邮箱登录
            $data = User::where("email","=",$username)->find();
            if ($data) {
                if (md5($password) == $data->password) {
                    if ($data->status == 1) {
                        //查询用户详情
                        $userinfo = Userinfo::where("user_id","=",$data->id)->find();
                        //查询用户收藏
                        $collection = User_collection::where("user_id","=",$data->id)->select();
                        //定义收藏空数组
                        $shoucang = array();
                        // 把用户收藏的商品的id加到收藏数组
                        foreach ($collection as $k => $v) {
                            $shoucang[$k] = $v->goods_id;
                        }
                        //把用户的名字加到session,以通过中间件验证
                        session('user_name',$userinfo->name);
                        //把用户id加到session方便查询
                        session('uid',$userinfo->user_id);
                        //把用户头像加到session方便查询
                        session('pic',$userinfo->pic);
                        //把用户收藏的商品的id加到session方便查询
                        session('shoucang',$shoucang);
                        return $this->redirect("/user");
                    }else{
                        return $this->error('您的账号尚未激活');
                    }
                }else{
                    return $this->error('账号或密码错误');;//就是密码错误
                }
            }else{
                //手机号登录
                $data = User::where("phone","=",$username)->find();
                if ($data) {
                    if (md5($password) == $data->password){
                        //查询用户详情
                        $userinfo = Userinfo::where("user_id","=",$data->id)->find();
                        //查询用户收藏
                        $collection = User_collection::where("user_id","=",$data->id)->select();
                        //定义收藏空数组
                        $shoucang = array();
                        // 把用户收藏的商品的id加到收藏数组
                        foreach ($collection as $k => $v) {
                            $shoucang[$k] = $v->goods_id;
                        }
                        //把用户的名字加到session,以通过中间件验证
                        session('user_name',$userinfo->name);
                        //把用户id加到session方便查询
                        session('uid',$userinfo->user_id);
                        //把用户头像加到session方便查询
                        session('pic',$userinfo->pic);
                        //把用户收藏的商品的id加到session方便查询
                        session('shoucang',$shoucang);
                        return $this->redirect("/user");
                    }else{
                        return $this->error('账号或密码错误');;//就是密码错误
                    }
                }else{
                    return $this->error('账号不存在');;//空
                }
            }
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

}
