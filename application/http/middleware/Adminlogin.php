<?php
namespace app\http\middleware;

class Adminlogin
{
    use \traits\controller\Jump;
    public function handle($request, \Closure $next)
    {
    	//检测当前有没有登录的session信息
        if (!empty(session('admin_name'))) {
            $controller=request()->controller();
            $action=request()->action();
            $nodelist = session("nodelist");
            if (empty($nodelist[$controller]) || !in_array($action,$nodelist[$controller])) {
                return $this->error('非常抱歉,你的权限不足');
            }
            return $next($request);
        }else{
            //跳转到登录界面 ndoejs也是用redirect跳转
            return view("login/login");
        }
    }
}
