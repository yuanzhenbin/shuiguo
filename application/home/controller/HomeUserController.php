<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Userinfo;

class HomeUserController extends Controller
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
        //会员修改自己的个人信息
        $id = $request->post('id');
        $data = $request->post();
        unset($data['id']);
        // var_dump($data);exit;
        $file = request()->file('pic');
        $info=Userinfo::where("user_id",'=',$id)->find();
        //图片上传
        if ($file) {        //有图
            $info = $file->move( './upload/user');
            $data['pic'] = '/upload/user/'.$info->getSaveName();
            // var_dump($data);
            try{
                Userinfo::update($data,['user_id'=>$id],true);
            }catch(\Exception $e){
                return $this->error("修改图片失败");
            }
            //执行修改
            if (!empty($data['name'])) {
                session('user_name',$data['name']);
            }

            if (empty(session('pic'))) {//如果没有原图，不删除
                session('pic',$data['pic']);
                echo "<script>alert('修改成功');location='/user_info'</script>";
                // return redirect("/user_info");
            }else{//如果有原图，把原图删除
                unlink(".".$info->pic);
                // $request->session()->pull("pic");
                session('pic',$data['pic']);
                echo "<script>alert('修改成功');location='/user_info'</script>";
                // return redirect("/user_info")->with('success','修改成功');
            }
        }else{              //无图
            if (Userinfo::update($data,['user_id'=>$id],true)) {
                if (!empty($data['name'])) {
                    session('user_name',$data['name']);
                }
                echo "<script>alert('修改成功');location='/user_info'</script>";
                // return redirect("/user_info")->with('success','修改成功');
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

}
