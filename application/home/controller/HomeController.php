<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\Pic;
use app\common\model\Goods;
use app\common\model\Goods_type;
use app\common\model\Goods_jifen;
use app\common\model\Orders;
use app\common\model\Orders_info;
use app\common\model\Orders_jifen;
use app\common\model\Connection;
use app\common\model\Message;
use app\common\model\User;
use app\common\model\Userinfo;
use app\common\model\User_address;
use app\common\model\User_collection;

class HomeController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //前台首页
        $pic = Pic::select();
        return view("/index",["pic"=>$pic]);
    }

    //前台登录
    public function login(){
        return view("/Login");
    }

    //个人中心
    public function user(){
        //根据uid查询，遍历到个人中心
        $uid = session('uid');
        $data = Userinfo::where("user_id","=",$uid)->find();
        $orders = Orders::where("user_id","=",$uid)->order('id','desc')->paginate(3);
        $fh = 0;
        foreach ($orders as $key => $value) {
            if ($value->fahuo == 1) {
                $fh++;
            }
        }
        foreach ($orders as $key => $value) {
            $orders[$key]->goods = Orders_info::where("orders_id","=",$value->id)->select();
        }
        // dd($orders);
        return view("/user",["data"=>$data,"orders"=>$orders,"fh"=>$fh]);
    }

    //个人信息
    public function user_info(){
        //执行查询，根据uid查资料，把结果集遍历到信息页input框的value值里
        $uid = session('uid');
        $data = Userinfo::where("user_id","=",$uid)->find();
        // var_dump($data->pic);exit;
        return view("/user_info",["data"=>$data]);
    }

    //修改密码
    public function user_password(){
        //
        return view("/user_Password");
    }
    
    //我的积分
    public function user_integral(){
        //执行查询，根据uid查资料，把结果集遍历到信息页input框的value值里
        $uid = session('uid');
        //查询积分
        $jifen = Userinfo::where("user_id","=",$uid)->find()->jifen;
        //查询积分订单
        $data = Orders_jifen::where("user_id","=",$uid)->select();
        return view("/user_integral",["data"=>$data,'jifen'=>$jifen]);
    }

    //我的评论
    // public function user_pinglun(){
    //     //dd($id);
    //     //$comment = Db::table('message')->where('user_id','=',$id)->paginate(5);
    //     //连接数据库  获取 本用户的评论数据
    //     $comment = Db::table('message')->join('goods','goods.id','=','message.goods_id')->where('user_id','=',session('uid'))->select();
    //     //dd($comment);
    //     //导入页面
    //     return view("Web.user_comment",["comment"=>$comment]);
    // }
    // ajax分页 我的评论-----------------------------------------------------------------------
    public function user_pinglun(Request $request){
        //
        $uid=session('uid');
        // 获取数据总条数
        $tot = Message::where("user_id","=",$uid)->count();
        // 设置每页评论条数
        $rev=5;
        // 获取总页数
        $sums=ceil($tot/$rev);
        // var_dump($sums);exit;
        // 获取附加参数
        $page = $request->post('page');
        // 判断page为空
        if(empty($page)){
            $page=1;
        }
        // 跳过多少条取多少条 偏移量
        $offset=($page-1)*$rev;
        // 准备sql语句
        $sql="select cm.content,cm.addtime,g.goods_name,g.pic from message as cm,goods as g where cm.goods_id=g.id and cm.user_id='{$uid}' limit $offset,$rev";
        // 执行sql
        $comment = Db::query($sql);
        // 判断当前请求是否为ajax请求
        if($request->isAjax()){
            return view('home/Page/user_comment',["comment"=>$comment]);
        }
        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }
        // $comment=Db::select("select cm.connect,cm.created_at,g.goods_name from comment as cm,goods as g where cm.goods_id=g.id and cm.userinfo_name='{$name}'");
        // dd($comment);
        return view("/user_comment",["comment"=>$comment,'pp'=>$pp]);
    }

    //添加评论
    public function comment(Request $request){
        $id = $request->post('id');
        $order_id = $request->post('order_id');
        // var_dump($id);
        // var_dump($order_id);exit;
        //连接数据库获取商品数据库
        $res = Goods::where('id','=',$id)->find();
        //导入摸板
        // var_dump($res->goods_name);exit;
        return view('/comment/index',['c_res'=>$res,'order_id'=>$order_id]);
    }
    //保存评论
    public function comments(Request $request){

        $order_id = $request->post('order_id');
        $data = $request->post();
        // var_dump($data);exit;
        if (!$data['content']) {
            return $this->error('评论内容不能为空');
        }
        $data['user_id'] = session('uid');
        $data['addtime'] = date('Y-m-d H:i:s',time());
        //连接数据库保存数据
        try{
            Message::create($data);
        }catch(\Exception $e){
            return $this->error('请重新评论');
        }

        $arr['pinglun'] = 1;
        $id = Orders_info::where("orders_id","=",$order_id)->where("goods_id","=",$data['goods_id'])->find()->id;
        Orders_info::update($arr,['id'=>$id],true);
        return $this->redirect('/user_Orders');
    }

    //ajax分页 历史订单
    public function user_Orders(Request $request){
        //执行查询
        $uid = session('uid');
        // dd($data);
        $tot = Orders::where("user_id","=",$uid)->count();
        // 规定下每页显示的数据条数
        $rev = 3;
        // 获取总页数
        $sums = ceil($tot/$rev);
        // 获取附加参数值
        $page=$request->post('page');
        // 判断page为空
        if(empty($page)){
            $page=1;
        }
        // 偏移量
        $offset=($page-1)*$rev;
        // 准备sql语句
        $sql="select * from orders where user_id={$uid} limit $offset,$rev";
        // 执行sql
        $data=Db::query($sql);
        // var_dump($data);exit;
        foreach ($data as $key => $value) {
            $data[$key]['goods'] = Orders_info::where("orders_id","=",$value['id'])->select();
        }
        // 判断当前请求是否为ajax请求
        if($request->isAjax()){
            return view("page/user_Orders",['data'=>$data]);
        }
        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }
        //whereIn查询
        // $data = Db::table("goods")->whereIn("id",$arr)->paginate(8);;
        // $data = Db::select("select * from goods where id in ".$str);
        return view("/user_Orders",['data'=>$data,"pp"=>$pp]);
    }

    //我的收藏
    // public function user_collection(Request $request){
    //     //执行查询，根据uid查收藏信息
    //     $goods = Db::table("user_collection")->where("user_id","=",session('uid'))->select();
    //     $arr = array();//定义空数组防止报错
    //     foreach ($goods as $key => $value) {
    //         $arr[$key] = $value->goods_id;
    //     }
    //     //whereIn查询
    //     $data = Db::table("goods")->whereIn("id",$arr)->select();;
    //     // $data = Db::select("select * from goods where id in ".$str);

    //     return view("Web.user_Collect",["data"=>$data,"request"=>$request->all(),]);
    // }
    // ajax分页 我的收藏----------------------------------------------------------------------------
    public function user_collection(Request $request){
        //执行查询，根据uid查收藏信息
        $uid = session('uid');
        $goods = User_collection::where("user_id","=",$uid)->select();
        $arr = array();//定义空数组防止报错
        foreach ($goods as $key => $value) {
            $arr[$key] = $value->goods_id;
        }
        $str=implode(',',$arr);
        if (empty($str)) {
            $str = 0;
        }
        // // 获取数据总条数
        $tot = User_collection::where("user_id","=",$uid)->count();
        // 规定下每页显示的数据条数
        $rev = 8;
        // 获取总页数
        $sums = ceil($tot/$rev);
        // 获取附加参数值
        $page=$request->post('page');
        // 判断page为空
        if(empty($page)){
            $page=1;
        }
        // 偏移量
        $offset=($page-1)*$rev;
        // 准备sql语句
        $sql="select * from goods where id in ($str) limit $offset,$rev";
        // 执行sql
        $data=Db::query($sql);
        // 判断当前请求是否为ajax请求
        if($request->isAjax()){
            return view("page/user_collection",['data'=>$data]);
        }
        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }
        //whereIn查询
        // $data = Db::table("goods")->whereIn("id",$arr)->paginate(8);;
        // $data = Db::select("select * from goods where id in ".$str);
        return view("/user_Collect",['data'=>$data,"pp"=>$pp]);
    }

    //删除收藏
    public function del_collection($id){
        //执行查询，根据uid查收藏信息
        $id = User_collection::where("goods_id","=",$id)->where("user_id","=",session('uid'))->find()->id;
        if (User_collection::where("id","=",$id)->delete()) {
            //--------------------
            //重置收藏的session信息
            //--------------------
            $collection = User_collection::where("user_id","=",session('uid'))->select();
            // var_dump($collection);exit;
            //定义收藏空数组
            $shoucang = array();
            // 把用户收藏的商品的id加到收藏数组
            foreach ($collection as $k => $v) {
                $shoucang[$k] = $v->goods_id;
            }
            session('shoucang',$shoucang);
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
        
    }  

    //活动专区（积分商城）
    public function integral(){
        //获取积分商品信息
        $data = Goods_jifen::select();
        //执行查询，根据uid查积分
        if (empty(session('uid'))) {//如果没登录
            //定义一个空对象
            $userinfo = new \StdClass;
            $userinfo->name = '游客';
            $userinfo->jifen = 0;
            return view("/integral",["data"=>$data,"userinfo"=>$userinfo]);
        }else{//登录
            $uid = session('uid');
            $userinfo = Userinfo::where("user_id","=",$uid)->find(); 
            return view("/integral",["data"=>$data,"userinfo"=>$userinfo]);
        } 
    }

    //所有果蔬页
    public function Products(){
        $data = Goods_type::where("pid","=",0)->select();
        $sg = Goods::where("lei","=",1)->select();
        $sc = Goods::where("lei","=",2)->select();
        $gg = Goods::where("lei","=",3)->select();
        return view("/Products",["data"=>$data,"sg"=>$sg,"sc"=>$sc,"gg"=>$gg]);
    }

    //商品一级列表页
    public function Products_list($id){
        //顶级类别的类名
        $result = Goods_type::where('id','=',$id)->find();
        //二级分类遍历
        $data = Goods_type::where('pid','=',$id)->select();
        // var_dump($data);exit;
        // 根据二级分类的id查出三级分类
        $goods = array();
        foreach($data as $key=>$value){
            $goods[$key] = Goods_type::where('pid','=',$value->id)->find();
        }
        //根据三级分类的id查出这个分类第一个商品的图片
        $pic = array();
        foreach($goods as $k=>$v){
            // var_dump($v);
            if ($v) { 
                $res = Goods::where('type_id','=',$v->id)->find();
                if ($res) {
                    $pic[$k] = $res->pic;
                }else{
                    $pic[$k] = '';
                }
            }else{
                $pic[$k] = '';
            }    
        }
        // var_dump($pic);exit;
        // 最后把这个图片的地址加到二级分类中，以便在前台显示
        foreach($pic as $k=>$v){
            $data[$k]->pic = $v;
        }
        // var_dump($result->type_name);exit;
        //销量排行
        $commend = Db::query("select * from goods order by sales desc limit 5");
        // var_dump($commend);exit;
        return view("/Product-List",["tpname"=>$result,"data"=>$data,'commend'=>$commend]);
    }

    //商品二级列表页
    public function Products_lists($id){
        //二级类别的类名
        $result = Goods_type::where('id','=',$id)->find();
        //三级分类遍历
        $data = Goods_type::where('pid','=',$id)->select();
        // var_dump($data);exit;
        // 根据三级分类的id查出所有该分类的商品
        foreach($data as $k=>$v){
            $data[$k]->goods = $goods = Goods::where("type_id","=",$v->id)->select();
        }
        // dd($data);
        // var_dump($data);exit;
        //销量排行
        $commend = Db::query("select * from goods order by sales desc limit 5");
        return view("/Product-lists",['tpname'=>$result,"data"=>$data,'commend'=>$commend]);
    }

    //商品详细信息
    // public function Product_detailed($id){
    //     //二级类别的类名
    //     $data = Db::table('goods')->where('id','=',$id)->find();
    //     //销量排行
    //     $commend = Db::select("select * from goods order by sales desc limit 5");
    //     $message = array();
    //     /*$message = Db::select("select u.name,u.pic,u.user_id,m.goods_id,m.content,m.addtime from user_info as u,message as m where u.user_id=m.user_id  and m.goods_id={$id}");*/
    //     $message = Db::table('user_info')->join('message','message.user_id','=','user_info.user_id')->where('message.goods_id','=',$id)->select('user_info.name','user_info.user_id','user_info.pic','message.goods_id','message.content','message.addtime')->orderBy('addtime','desc')->paginate(3);
    //     $count = count(Db::table('user_info')->join('message','message.user_id','=','user_info.user_id')->where('message.goods_id','=',$id)->select('user_info.name','user_info.user_id','user_info.pic','message.goods_id','message.content','message.addtime')->orderBy('addtime','desc')->select());
    //     return view("Web.Product-detailed",["data"=>$data,'message'=>$message,'count'=>$count,'commend'=>$commend]);
    // }
    //ajax分页 商品详细信息------------------------------------------------------------------------
    public function Product_detailed(Request $request,$id){
        //二级类别的类名
        $data = Goods::where('id','=',$id)->find();
        // 商品评论
        // // 获取数据总条数
        $tot = Message::where("goods_id","=",$id)->count();
        // 规定下每页显示的数据条数
        $rev = 3;
        // 获取总页数
        $sums = ceil($tot/$rev);
        // 获取附加参数值
        $page=$request->post('page');
        // 判断page为空
        if(empty($page)){
            $page=1;
        }
        // 偏移量
        $offset=($page-1)*$rev;
        // var_dump($page);exit;
        // 准备sql语句
        $sql="select * from message as cm,user_info as ui where ui.user_id=cm.user_id and cm.goods_id={$id} limit $offset,$rev";
        // 执行sql
        $comment=Db::query($sql);
        // 判断当前请求是否为ajax请求
        if($request->isAjax()){
            return view("page/product_detailed",['comment'=>$comment]);
        }
        $pp = array();
        for($i=1;$i<=$sums;$i++){
            $pp[$i]=$i;
        }
        // $comment=Db::select("select * from comment as cm,user_info as ui where ui.name=cm.userinfo_name and cm.goods_id={$id}");
        // dd($comment);
        $commend = Db::query("select * from goods order by sales desc limit 5");

        return view("/Product-detailed",["data"=>$data,"pp"=>$pp,'id'=>$id,'comment'=>$comment,'commend'=>$commend]);
    }

    //收藏商品
    public function collection($id){
        //
        $data['user_id'] = session('uid');
        $data['goods_id'] = $id;
        try{
            User_collection::create($data,true);
        }catch(\Exception $e){
            return $this->error('出现错误，请稍后再试');
        }
        //--------------------
        //重置收藏的session信息
        //--------------------
        $collection = User_collection::where("user_id","=",session('uid'))->select();
        //定义收藏空数组
        $shoucang = array();
        // 把用户收藏的商品的id加到收藏数组
        foreach ($collection as $k => $v) {
            $shoucang[$k] = $v->goods_id;
        }
        session('shoucang',$shoucang);
        return $this->redirect('/user_Collect');
    }

    //商品搜索
    public function search(Request $request){
        //dd($request->all());
        //
        $g = 1;
        //获取传过来的 keywords 值
        $k = $request->get('keywords');
        // var_dump($request->get());exit;
        //连接数据库 获取商品信息
        $goods =  Goods::where('goods_name','like','%'.$k.'%')->select();
        //判断goods 是否为空
        if (!count($goods)) {
            $goods = Goods::select();
            $g = 0;
        }
        //dd($goods);
        $commend=Db::query("select * from goods order by sales desc limit 5");
        return view('/Product-lists-search',['goods'=>$goods,'k'=>$k,'g'=>$g,'commend'=>$commend]);
    }
}
