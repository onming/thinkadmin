<?php

namespace app\admin\model\game;

use think\Model;
use Think\DB;
use think\Lang;
class User extends Model
{
    public function getList($param){
        $model = Db::connect($param['gameInfo']['mysql_read_config']);
        switch($param['gameInfo']['id']){
            case 5:
//                Lang::load("/application/admin/lang/zh-cn/ql/game.php");
//                halt();
                halt(Lang::load("/application/admin/lang/zh-cn/ql/game.php"));
                $list = $model->table('view_userlist')->where(['GameType'=>$param['gameInfo']['id']])->limit(1)->select()[0];
                foreach($list as $key=>$value){
                    $this->getField($key);
                }
                break;
            case 32:
                break;
            case 33:
        }
    }
    private function getField($key){
    }

}
