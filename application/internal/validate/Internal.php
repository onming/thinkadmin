<?php

namespace app\internal\validate;

use think\Validate;

class Internal extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'auth_id' => 'require|max:10',
        'module' => 'require',
        'action' => 'require',
        'timestamp' => 'require|max:10',
        'sign' => 'require|max:32',
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
        'default'  => ['auth_id', 'data', 'timestamp'],
        'old'  => ['auth_id', 'module', 'action', 'timestamp', 'sign'],
    ];

}
