<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\User;
use app\common\model\Userinfo;

class UserController extends Controller
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
        $keywords = '';
        // var_dump($request->get('keywords'));exit;
        if (!empty($request->get('keywords'))) {
            $tiaojian[] = ['username','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = User::where($tiaojian)->paginate(10)->appends($_GET);
        // var_dump($user);exit;
        return view("user/index",["data"=>$data,"keywords"=>$keywords]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //用户添加
        return view("user/add");
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
        $data = $request->post();
        $data['password'] = md5($data['password']);
        $data['token'] = rand(1,10000);
        $data['created_at'] = date('Y-m-d H:i:s',time());
        $arr['name'] = $name = $data['username'];

        try{
            User::create($data,true);
        }catch(\Exception $e){
            return $this->error("添加失败");
        }
        $user = User::where("username","=",$name)->select();
        $id = $user[0]->id;
        // var_dump($id);exit;
        $arr['user_id'] = $id;
        Userinfo::create($arr,true);
        return $this->success("添加成功",'/adminuser');
        
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
        //修改
        $data = User::find($id);
        $username = $data->username;
        $status = $data->status;
        // echo $user;
        return view("user/edit",["id"=>$id,"status"=>$status,"username"=>$username]);
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
        //执行修改
        $data = $request->post();
        $data['updated_at'] = date('Y-m-d H:i:s',time());
        // var_dump($data);exit;
        try{
            User::update($data,['id'=>$id],true);
        }catch(\Exception $e){
            return $this->error("修改失败");
        }
        return $this->success("修改成功",'/adminuser');
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
        $row = Admin::destroy($id);
        if ($row) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    //分配角色
    public function role($id){
        $user = Admin::find($id);
        $role = Role::select();
        $data = A_R::where("uid","=",$id)->select();
        if (count($data)) {
            $rid[] = $data[0]->rid;
            return view("admin/role",["user"=>$user,"role"=>$role,"rid"=>$rid]);
        }else{
            return view("admin/role",["user"=>$user,"role"=>$role,"rid"=>array()]);
        }
    }

    //执行分配角色
    public function dorole(){
        //获取修改角色所需要的uid和rid
        $rid = $_POST['rid'];
        $uid = $_POST["uid"];
        //放到数组中以便操作
        $data['uid'] = $uid;
        $data['rid'] = $rid;
        //把原角色删除，创建新角色
        A_R::where("uid","=",$uid)->delete();
        A_R::create($data,true);
        //返回管理员列表
        return $this->success("角色分配成功",'/admin');
    }
}
