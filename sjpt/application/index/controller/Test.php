<?php
/**
 * Created by PhpStorm.
 * User: Shinelon
 * Date: 2019/9/23
 * Time: 17:31
 */

namespace app\index\controller;

//use think\Controller;

use think\facade\Config;

class Test extends Authc
{
    public function index()
    {
        return 'test';


    }
}