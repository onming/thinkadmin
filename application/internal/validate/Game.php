<?php

namespace app\internal\validate;

use think\Validate;

class Game extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'table' => 'require',
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
        'gamevalue'  => ['table'],
    ];

}
