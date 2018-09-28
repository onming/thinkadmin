<?php

namespace app\admin\model\game;

use think\Model;
use think\Hook;

class Scene extends Model
{
    // 表名
    protected $name = 'game_scene';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
            $row->action = 'add';
            Hook::listen('scene_after', $row);
        });

        self::afterDelete(function ($row) {
            $row->action = 'del';
            Hook::listen('scene_after', $row);
        });
    }

    
    public function getStatusList()
    {
        return ['1' => __('Status 1'),'2' => __('Status 2')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function setSceneKeyAttr($value)
    {

        return strtolower($value);
    }



}
