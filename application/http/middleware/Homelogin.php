<?php
namespace app\http\middleware;

class Homelogin
{
    use \traits\controller\Jump;
    public function handle($request, \Closure $next)
    {
        //检测当前有没有登录的session信息
        if (!empty(session('user_name'))) {
            //获取访问模块控制器和方法名
            return $next($request);
        }else{
            //跳转到登录界面 ndoejs也是用redirect跳转
            return view("/Login");
        }
    }
}
