<?php

namespace app\admin\validate\game;

use think\Validate;

class Channel extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'channel_name'  => 'require',
        'channel_key'  => 'require|uniqueChannelKey',
        'game_id'  => 'require|max:10',
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
        'add'  => ['channel_name', 'channel_key', 'game_id'],
        'edit' => ['channel_name', 'channel_key'=>'require|uniqueChannelKey:edit'],
    ];

    /**
     * 自定义验证
     */
    protected function uniqueChannelKey($value, $rule, $data)
    {
        $where = [
            'channel_key' => $value,
            'game_id' => $data['game_id'],
        ];
        if($rule == 'edit'){
            $where['id'] = ['neq', $data['id']];
        }
        $channel_key = \app\admin\model\game\Channel::where($where)->limit(1)->column('id,channel_key');

        //halt($channel_key);
        if($channel_key){
            return '渠道已存在';
        }
        return true;
    }
    
}
