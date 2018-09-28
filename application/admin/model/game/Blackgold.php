<?php

namespace app\admin\model\game;

use think\Model;

class Blackgold extends Model
{
    // 表名
    protected $name = 'game_blackgold';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
//            $pk = $row->getPk();
//            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getStatusList()
    {
        return ['1' => __('Status 1'),'2' => __('Status 2')];
    }



}
