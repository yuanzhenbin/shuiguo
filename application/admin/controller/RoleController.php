<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\model\Admin;
use app\common\model\Role;
use app\common\model\A_R;
use app\common\model\Node;
use app\common\model\R_N;

class RoleController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        $role = Role::select();
        // var_dump($role);exit;
        return view("role/index",["role"=>$role]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //用户添加
        return view("role/add");
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //执行添加权限
        $data = $request->post();
        try{
            Node::create($data);
        }catch(\Exception $e){
            return $this->error("添加失败");
        }
        return $this->success("添加成功",'/role');
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
        //分配权限
        $role = Role::find($id);
        $node = Node::select();
        $data = R_N::where("rid","=",$id)->select();
        if (count($data)) {
            foreach($data as $v){
                $nids[] = $v->nid;
            }
            return view("role/edit",["role"=>$role,"node"=>$node,"nids"=>$nids]);
        }else{
            return view("role/edit",["role"=>$role,"node"=>$node,"nids"=>array()]);
        }
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
        //执行修改权限
        //获取修改角色所需要的uid和rid
        $rid = $id;
        $nid = $request->post("nid");
        //把原角色删除，创建新角色
        R_N::where("rid","=",$rid)->delete();
        foreach($nid as $key=>$v){
            //封装要插入的数据
            $data['rid']=$rid;
            $data['nid']=$v;
            //插入
            R_N::create($data);
        }       
        //返回管理员列表
        return $this->success("角色分配成功",'/role');
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

}
