<?php

namespace app\admin\validate\game;

use think\Validate;

class PacketConfig extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require|max:100',
        'type' => 'require|number|in:1,2',
        'money' => 'require|number',
        'exchange_noney' => 'require|number',
        'content' => 'max:255',
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
        'add'  => [],
        'edit' => [],
    ];
    
}
