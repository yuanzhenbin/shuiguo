<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;

class HomePasswordController extends Controller
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
        //执行修改
        $id = $request->post('id');
        $result = User::where("id","=",$id)->find();
        $oldpassword = $request->post("oldpassword");
        $data['password'] = $request->post("password");
        //加密密码
        $data['password'] = md5($data['password']);
        //判断旧密码
        if (md5($oldpassword) == $result->password) {
            //执行修改
            if(User::update($data,['id'=>$id],true)){
                echo "<script>alert('修改成功');location='/user_Password'</script>";
                // return redirect("/user_Password")->with('success','修改成功');
            }
        }else{
            echo "<script>alert('旧密码不正确');location='/user_Password'</script>";
            // return back()->with('error','旧密码不正确');
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
    public function update(Request $request)
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
