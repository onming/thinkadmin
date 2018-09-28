<?php

namespace app\admin\validate\game;

use think\Validate;

class Blackgold extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'level' => 'require|number',
        'discount' => 'number|between:0,1',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'level.require' => '等级不能为空',
        'level.number'  => '等级必须为数字',
        'discount.number' => '优惠力度必须为数字',
        'discount.between' => '优惠力度在0-1之间',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
    
}
