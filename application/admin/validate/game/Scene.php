<?php

namespace app\admin\validate\game;

use think\Validate;

class Scene extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'scene_name'  => 'require',
        'scene_key'  => 'require|uniqueSceneKey',
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
        'add'  => ['scene_name', 'scene_key', 'game_id'],
        'edit' => ['scene_name', 'scene_key' => 'require|uniqueSceneKey:edit'],
    ];

    /**
     * 自定义验证
     */
    protected function uniqueSceneKey($value, $rule, $data)
    {
        $where = [
            'scene_key' => $value,
            'game_id' => $data['game_id'],
        ];
        if($rule == 'edit'){
            $where['id'] = ['neq', $data['id']];
        }

        $scene_key = \app\admin\model\game\Scene::where($where)->limit(1)->column('id,game_id,scene_key');

        if($scene_key){
            return '场景已存在';
        }
        return true;
    }
    
}
