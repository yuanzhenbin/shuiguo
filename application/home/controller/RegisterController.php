<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\captcha\Captcha;

use app\common\model\User;
use app\common\model\Userinfo;

class RegisterController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        return view('/registered');
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
        //执行注册
        // $code = $request->post('code');
        // $vcode = session('vcode');
        // if($code == $vcode){
            $data = $request->post();
            $data['password'] = md5($data['password']);
            $data['token'] = rand(1,10000);
            $arr['name'] = $data['username'];
            // var_dump($data);exit;
            if(User::create($data,true)){
                $id = User::where("username","=",$data['username'])->find()->id;
                $arr['user_id'] = $id;
                //同步创建会员详情表基本信息
                Userinfo::create($arr,true);
                //向注册的邮箱发送邮件 激活用户 激活会状态0改为2
                $res = sendMail($data['email'],$id,$data['token']);
                if($res){
                    echo "<script>alert('请登录邮箱激活账号后登录');location='/login'</script>";
                    // return redirect("/login")->with('success','添加成功');
                }
            }
        // }else{
        //     return $this->error('校验码有误');
        // } 
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

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    // 发送纯文本 识图和数据 $email 接收方 $id 注册用户 $token 检验参数
    public function sendMail($email,$id,$token){
        // 在闭包函数内部使用闭包函数外部的变量 必须use导入 a是模板
        Mail::send('Web.Register.activtion',['id'=>$id,'token'=>$token],function($message)use($email){
            $message->to($email);
            $message->subject('激活用户');
        });
        return true;
    }

    //执行激活
    public function activtion(Request $request){
        $id=$request->post('id');
        $token=$request->post('token');
        $info=User::where('id','=',$id)->find();
        if($token==$info->token){
            $data['status']=1;
            // 给token赋值
            $data['token']=rand(1,1000);
            User::update($data,['id'=>$id],true);
            echo "<script>alert('激活成功');location='/login'</script>";
        }
        // echo "id".$id;
    }

    public function code(Request $request){
        $code = $request->get('code');
        $captcha = new Captcha();
        if (!$captcha->check($code)){
            echo 2;
        }else{
            echo 1;
        }
    }

}
