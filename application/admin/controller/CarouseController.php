<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Pic;

class CarouseController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        $data = Pic::select();
        // var_dump($data[0]);exit;
        return view("carouse/index",["data"=>$data]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return view('carouse/add');
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
        $file = request()->file('pic');
        $info = $file->move( './upload/lunbo');
        $data['pic'] = '/upload/lunbo/'.$info->getSaveName();
        // var_dump($data);
        try{
            Pic::create($data,true);
        }catch(\Exception $e){
            return $this->error("添加失败");
        }
        return $this->success("添加成功",'/carouse');  

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
        $data = Pic::find($id);
        $pic = $data->pic;
        // var_dump($pic);exit;
        //dd($pic);
        if ($pic) {
            //删除图片
            unlink('.'.$pic);
        }
        $row = Pic::destroy($id);
        if ($row) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}
