<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Orders_jifen;
use app\common\model\Orders;

class JifenOrdersController extends Controller
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
            $tiaojian[] = ['user_name','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = Orders_jifen::where($tiaojian)->order('user_id')->paginate(10)->appends($_GET);
        // var_dump($data[0]);exit;
        return view("orders/jifen",["data"=>$data,"keywords"=>$keywords]);
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
        $data = Orders_jifen::find($id);
        return view('orders/jifenedit',['data'=>$data]);
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
        try{
            Orders_jifen::update($data,['id'=>$id],true);
        }catch(\Exception $e){
            return $this->error("修改失败");
        }
        return $this->success("修改成功",'/adminjifen');
        
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
