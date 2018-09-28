<?php

namespace app\admin\validate\game;

use think\Validate;

class GameSwitch extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'channel_id' => 'require|number',
        'scene_id' => 'require|number',
        'vip_level' => 'require|number',
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
