<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use think\captcha;

class ForgetController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
        return view('forget/forget');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        return view('home/Login');
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
        //验证登录
        $code = $request->post('code');
        $vcode = session('vcode');
        if($code == $vcode){
            $email = $request->post('email');
            $info = DB::table('user')->where('email','=',$email)->first();
            if ($info) {
                // 发送邮件
                $res = $this->sendMail($email,$info->id,$info->token);
                if($res){
                    echo "<script>alert('重置密码邮件已发送,请登录邮箱重置密码');location='/login'</script>";
                    // echo "重置密码邮件已发送请登录邮箱重置密码";
                }
            }else{
                echo "<script>alert('邮箱有误');location='/forget'</script>";
            }     
        }else{
            echo "<script>alert('验证码有误');location='/forget'</script>";
            // return redirect("/forget")->with('error','校验码有误');
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

    public function sendMail($email,$id,$token){
        // 在闭包函数内部使用闭包函数外部的变量 必须use导入 a是模板
        Mail::send('forget/reset',['id'=>$id,'token'=>$token],function($message)use($email){
            $message->subject('重置密码');
            $message->to($email);
        });
        return true;
    }

    public function reset(Request $request){
        $id=$request->post('id');
        $info=User::where('id','=',$id)->find();
        $token=$request->post('token');
        // 对比邮件的token和数据表的token
        if($token==$info->token){
            return view('forget/doreset',['id'=>$id]);
        }
    }

    public function doreset(Request $request){
        $password = $request->post('password');
        $id = $request->post('id');
        $data['password'] = md5($password);
        // 重新赋值
        $data['token'] = rand(1,10000);
        if(User::update($data,['id'=>$id],true)){
            echo "<script>alert('重置密码成功,请使用新密码登录');location='/login'</script>";
        }
    }

    public function code(){
         //生成校验码代码
         ob_clean();
         //清除操作
         $builder = new Captcha;
         ///可以设置图片宽高及字体
         $builder->build($width = 100, $height = 40, $font = null);
         ///获取验证码的内容
         $phrase = $builder->getPhrase();
         ///把内容存入session
        session(['vcode'=>$phrase]);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        // 输出校验码
        $builder->output();
    }

}
