<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Admin;

class LoginController extends Controller
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
        session("admin_name",null);
        session("role_name",null);
        session("nodelist",null);
        // session(null);
        return view("login/login");
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
    public function test(Request $request)
    {
        //
        // var_dump($request->post());exit;
        echo "string";
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
        $password = $request->post('password');
        $admin_name = $request->post('admin_name');
        $data = Admin::where("admin_name","=",$admin_name)->find();
        if ($data) {
            if (md5($password) == $data->password) {
                //把用户的名字加到session,以通过中间件验证
                session('admin_name',$admin_name);
                $sql = "select ar.rid,node_name,cname,fname from admin_role as ar,role_node as rn,node where ar.rid=rn.rid and rn.nid=node.id and ar.uid={$data->id}";
                $result = Db::query($sql);
                // var_dump($result);exit;
                // 默认所有人都能访问后台首页
                $nodelist["AdminController"][] = "adminindex";
                // var_dump($result);exit;

                foreach ($result as $k => $v) {
                    $nodelist[$v['cname']][] = $v['fname'];
                    //如果有添加，加上执行添加
                    if ($v['fname'] == "create") {
                        $nodelist[$v['cname']][] = "save";
                    }
                    //如果有修改，加上执行修改
                    if ($v['fname'] == "edit") {
                        $nodelist[$v['cname']][] = "update";
                    }
                }
                //把用户的角色添加到session
                if($result){
                    $rid = $result[0]['rid'];
                    $sql = "select role_name from role where id={$rid}";
                    $role_name = Db::query($sql);
                    session('role_name',$role_name[0]['role_name']);
                }else{
                    session('role_name',"无权限者");
                }
                // session(['role_name'=>"root"]);
                //把用户角色的权限加到session,以判断可以进哪些页面
                session('nodelist',$nodelist);
                return view("login/index");
            }else{
                return $this->error('用户名或密码错误');
            }  
        }else{
            return $this->error('此账号不存在');
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

    public function login()
    {
        //
        return view("login/login");
    }
}
