<?php

namespace app\admin\model\game;

use think\Model;

class Game extends Model
{
    // 表名
    protected $name = 'game';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'env_text',
        'refreshtime_text',
        'status_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
//            $pk = $row->getPk();
//            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getEnvList()
    {
        return ['production' => __('Env production'),'stress' => __('Env stress'),'test' => __('Env test'),'dev' => __('Env dev'),'work' => __('Env work')];
    }     

    public function getStatusList()
    {
        return ['1' => __('Status 1'),'2' => __('Status 2')];
    }     


    public function getEnvTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['env']) ? $data['env'] : '');
        $list = $this->getEnvList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getRefreshtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['refreshtime']) ? $data['refreshtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setRefreshtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
