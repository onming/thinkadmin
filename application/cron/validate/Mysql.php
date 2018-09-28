<?php

namespace app\cron\validate;

use think\Validate;

class Mysql extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'day' => 'require|number|>=:7',
        'table' => 'require|in:admin_log',
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'removeData'  => ['day', 'table'],
    ];

}
