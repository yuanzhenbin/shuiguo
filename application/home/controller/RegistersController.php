<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use think\Db;

use app\common\model\User;
use app\common\model\Userinfo;

class RegistersController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        //
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

    public function checkphone(Request $request)
    {
        $p=$request->get('p');
        
        $data=User::select();
        foreach ($data as $k => $v) {
            $arr[$k] = $v->phone;
        }

        if(in_array($p,$arr)){
            echo 1;//可以注册
        }else{
            echo 0;
        }
    }

    public function sendphone(Request $request){
        $p=$request->get('pp');
        // echo $p;
        //调用短信接口
        funcs($p);
    }


    public function checkcode(Request $request)
    {
        //获取输入的校验码
        $code=$request->get('code');
        // var_dump(isset($_COOKIE['fcode']));exit;
        if(isset($_COOKIE['fcode']) && !empty($code)){
            //获取手机号收到的校验码
            $fcode=$_COOKIE['fcode'];
            if($fcode==$code){
                echo 1;//校验码一致
            }else{
                echo 2;//不一致
            }
        }elseif(empty($code)){
            echo 3;//为空
        }else{
            echo 4;//过期
        }
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
        $data = $request->post();
        $data['password'] = md5($data['password']);
        $data['token'] = rand(1,10000);
        //手机注册默认用户名
        $data['username'] = $data['phone'];
        $data['status'] = 1;
        $arr['name'] = $data['phone'];
        // var_dump($data);exit;
        if(User::create($data,true)){
            $id = User::where("username","=",$data['username'])->find()->id;
            $arr['user_id'] = $id;
            //同步创建会员详情表基本信息
            Userinfo::create($arr,true);
            echo "<script>alert('注册成功');location='/login'</script>";
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

}
