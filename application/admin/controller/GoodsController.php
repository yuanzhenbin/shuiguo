<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Goods_type;
use app\common\model\Goods;

class GoodsController extends Controller
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
            $tiaojian[] = ['goods_name','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = Goods::where($tiaojian)->order('type_id')->paginate(10)->appends($_GET);
        foreach ($data as $key => $value) {
            $data[$key]->type_name = Goods_type::where("id","=",$value->type_id)->find()->type_name;
        }
        // var_dump($data[0]);exit;
        return view("goods/index",["data"=>$data,"keywords"=>$keywords]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $type = Goods_type::select();
        foreach ($type as $key => $value) {
             //echo $value->path."<br>";
            $arr = explode(',',$value->path);
            $len = count($arr)-1;
            //echo $len.'<br>';
            $type[$key]->type_name = str_repeat('--|',$len).$value->type_name;
            //查询该类变是否有子分类 如果没有为0 可以选中
            $res = Goods_type::where('pid','=',$value->id)->find();
            //var_dump(count($res));
            //将长度复制给 $type['test']
            $type[$key]->test = count($res);
        }
        return view('goods/add',['type'=>$type]);
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
        $file = request()->file('pic');
        if ($file) {        //有图
            $info = $file->move( './upload/goods');
            $data['pic'] = '/upload/goods/'.$info->getSaveName();
            // var_dump($data);
            try{
                Goods::create($data,true);
            }catch(\Exception $e){
                return $this->error("添加失败");
            }
            return $this->success("添加成功",'/goods');  
        }else{              //无图
            try{
                Goods::create($data,true);
            }catch(\Exception $e){
                return $this->error("添加失败");
            }
            return $this->success("添加成功",'/goods'); 
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
        $data = Goods::find($id);
        $type = Goods_type::select();
        foreach ($type as $key => $value) {
             //echo $value->path."<br>";
            $arr = explode(',',$value->path);
            $len = count($arr)-1;
            //echo $len.'<br>';
            $type[$key]->type_name=str_repeat('--|',$len).$value->type_name;
            //查询该类变是否有子分类 如果没有为0 可以选中
            $res = Goods_type::where('pid','=',$value->id)->find();
            //var_dump(count($res));
            //将长度复制给 $type['test']
            $type[$key]->test = count($res);
        }
        $name = Goods_type::find($data->type_id)->type_name;
        // var_dump($name);exit;
        return view('goods/edit',['data'=>$data,'type'=>$type,'id'=>$id,'name'=>$name]);
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
            Goods_type::update($data,['id'=>$id],true);
        }catch(\Exception $e){
            return $this->error("修改失败");
        }
        return $this->success("修改成功",'/type');
        
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
        $data = Goods::find($id);
        $pic = $data->pic;
        // var_dump($pic);exit;
        //dd($pic);
        if ($pic) {
            //删除图片
            unlink('.'.$pic);
        }
        $row = Goods::destroy($id);
        if ($row) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}
