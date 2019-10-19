<?php
/**
 * Created by PhpStorm.
 * User: Shinelon
 * Date: 2019/9/23
 * Time: 17:17
 */

namespace app\index\controller;

use think\Controller;
use think\auth\Auth;


class Authc extends Controller
{
//    权限类，所有需要权限判断的类均继承此类
    protected function initialize()
    {

        if (!session('user.id') || empty(session('user.id'))) {
//  直接实例化index控制器,也可以直接跳转到登录页
//            $index = new \app\index\controller\Index();
//            $index->index();

            return $this->view->fetch('index/login/login');

        }

        $auth = new Auth();
//        $auth = Auth::instance();
        $controller = request()->controller();


        if (!$auth->check('Index/' . $controller . '/', session('user.id'))) {
            echo '你没有权限';
            exit();
        }
    }


}