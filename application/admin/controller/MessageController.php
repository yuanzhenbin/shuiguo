<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Goods;
use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\Message;

class MessageController extends Controller
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
            $keywords = $request->get('keywords');
            if(Userinfo::where('name','like','%'.$keywords.'%')->find()){
                $uid = Userinfo::where('name','like','%'.$keywords.'%')->find()->user_id;
                $tiaojian[] = ['user_id','=',$uid];
            }else{
                $tiaojian[] = ['user_id','=',''];
            }
        }
        $data = Message::where($tiaojian)->order('goods_id')->paginate(10)->appends($_GET);
        //商品id变商品名用户id变用户名
        foreach ($data as $key => $value) {
            $data[$key]->goods_name = Goods::where("id","=",$value->goods_id)->find()->goods_name;
            $data[$key]->user_name = Userinfo::where('user_id','=',$value->user_id)->find()->name;
        }
        // var_dump($data[0]);exit;
        return view("message/index",["data"=>$data,"keywords"=>$keywords]);
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
        $row = Message::destroy($id);
        if ($row) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}
