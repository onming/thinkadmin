<?php

namespace app\cron\validate;

use think\Validate;

class Mongo extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'day' => 'require|number|>=:7',
        'table' => 'require|in:debug,listen_sql,crontab_log',
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
