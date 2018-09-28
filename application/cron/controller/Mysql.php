<?php
/**
 * mysql定时脚本
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/6
 */
namespace app\cron\controller;

use app\common\controller\Cron;
use think\Db;

class Mysql extends Cron
{

    /**
     * 清空mysql数据
     *
     * @param string table
     * @param int day
     * @return array
     */
    public function removeData()
    {
        $input = $this->request->param();
        $validate = $this->validate($input, "Mysql.removeData");
        $this->input = $input;
        if($validate !== true){
            return $this->error($validate);
        }
        $time = time()-$input['day']*86400;
        Db::connect("mongo_db")->name($input['table'])->where("date","<=",date("Y-m-d H:i:s", $time))->delete();
        return $this->success("已清空{$input['table']}表{$input['day']}天前数据成功");
    }

}
