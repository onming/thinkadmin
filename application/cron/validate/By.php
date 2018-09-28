<?php

namespace app\cron\validate;

use think\Validate;

class By extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'gameid' => 'require|number',
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
        'default'  => ['gameid'],
    ];

}
