<?php

namespace app\admin\model\gamevalue;

use think\Model;

class TableSet extends Model
{
    // 表名
    protected $name = 'table_set';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

}
