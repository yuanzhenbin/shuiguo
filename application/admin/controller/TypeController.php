<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Goods_type;
use app\common\model\Goods;

class TypeController extends Controller
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
            $tiaojian[] = ['type_name','like','%'.$request->get('keywords').'%'];
            $keywords = $request->get('keywords');
        }
        $data = Goods_type::orderRaw('concat(path,",",id)')->where($tiaojian)->paginate(15)->appends($_GET);
        foreach ($data as $key => $value) {
            $arr = explode(',',$value->path);
            $len = count($arr)-1;
            $data[$key]->type_name = str_repeat('--|',$len).$value->type_name;
        }
        // var_dump($data);exit;
        return view("type/index",["data"=>$data,"keywords"=>$keywords]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $data = Goods_type::orderRaw('concat(path,",",id)')->paginate(15);
        foreach ($data as $key => $value) {
            $arr = explode(',',$value->path);
            $len = count($arr)-1;
            $data[$key]->type_name = str_repeat('--|',$len).$value->type_name;
        }
        return view('type/add',['data'=>$data]);
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
        // var_dump($data);exit;
        $pid = $data['pid'];
        if ($pid == 0) {//是否顶级分类
            $data['path'] = 0;
            try{
                Goods_type::create($data,true);
            }catch(\Exception $e){
                return $this->error("添加失败");
            }
            return $this->success("添加成功",'/type');  
        }else{
            $info = Goods_type::find($pid);
            $data['path'] = $info->path.','.$info->id;
            // var_dump($data);exit;
            try{
                Goods_type::create($data,true);
            }catch(\Exception $e){
                return $this->error("添加失败");
            }
            return $this->success("添加成功",'/type'); 
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
        $data = Goods_type::find($id);
        return view('type/edit',['data'=>$data]);
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
        $pid = Goods_type::where('pid','=',$id)->select();
        //判断返回值是否为空，如果为空则删除该商品分类
        if (count($pid)) {
            //echo 1;
            //该商品分类存在子分类，不能删除
            return $this->error('存在子分类，不能删除');
        }else{
            $row = Goods_type::destroy($id);
            if ($row) {
                return $this->success("删除成功");
            }else{
                return $this->error("删除失败");
            }
        }
    }
}
