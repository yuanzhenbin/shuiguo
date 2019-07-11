<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Connection;

class LinkController extends Controller
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
            $tiaojian[] = ['connection_name','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = Connection::where($tiaojian)->paginate(10)->appends($_GET);
        // var_dump($data[0]);exit;
        return view("link/index",["data"=>$data,"keywords"=>$keywords]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return view('link/add');
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
        try{
            Connection::create($data,true);
        }catch(\Exception $e){
            return $this->error("添加失败");
        }
        return $this->success("添加成功",'/links'); 

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
        $data = Connection::find($id);
        return view("link/edit",['data'=>$data]);
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
        $data = $request->post();
        // var_dump($data);exit;
        try{
            Connection::update($data,['id'=>$id],true);
        }catch(\Exception $e){
            return $this->error("修改失败");
        }
        return $this->success("修改成功",'/links'); 
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
        $row = Goods_jifen::destroy($id);
        if ($row) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}
