<?php
/**
 * 用户接口
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2016/6/30
 */
namespace app\internal\controller;

use app\common\controller\Internal;

class User extends Internal
{

    /**
     * 首页
     * 
     */
    public function index()
    {
        $this->success('请求成功');
    }

    /**
     * user list
     *
     */
    public function getUserList()
    {
        $data = array(

        );
        $this->success('success', $data);
    }

}
