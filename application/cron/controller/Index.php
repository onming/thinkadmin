<?php
/**
 * cron定时脚本
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/11
 */
namespace app\cron\controller;

use app\common\controller\Cron;

class Index extends Cron
{

    /**
     * 默认入口
     */
    public function index()
    {
        return $this->success("测试成功");
    }

}
