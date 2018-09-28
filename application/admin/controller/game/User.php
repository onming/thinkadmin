<?php
/**
 * 用户管理
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/09/26
 */
namespace app\admin\controller\game;

use app\common\controller\Backend;

class User extends Backend
{
    
    /**
     * Game模型对象
     * @var \app\admin\model\game\Game
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $this->tableFieldsInit();
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index(){
        return $this->view->fetch();
    }
    /**
     * 表字段初试化
     */
    private function tableFieldsInit()
    {
        $tableList = [];
        if(empty($this->gameInfo['game_id'])){
            $this->error("请先选择游戏");
        }
        $param = ['gameInfo' => $this->gameInfo];
        $model = new \app\admin\model\game\User;
        $model->getList($param);
//        halt($this->gameInfo['game_id']);

//        $pid = $this->request->request("pid");
//        $this->pid = !empty($pid)?$pid:(!empty($tableList[0]['id'])?$tableList[0]['id']:'');
//        $columns = [];
//        if($this->pid){
//            $result = collection(Db::name('table_set')->where('pid', '=', $this->pid)->field('title,name,type')->select())->toArray();
//
//            $columns[] = ['field' => 'state', 'checkbox' => true];
//            foreach ((array)$result as $row){
//                $columns[] = [
//                    'field' => $row['name'],
//                    'title' => $row['title'],
//                ];
//                $this->fields[] = $row;
//            }
//        }
//        $this->assign('pid', $this->pid);
//        $this->assign('fields', $this->fields);
//        $this->assignconfig('pid', $this->pid);
//        $this->assignconfig('columns', $columns);
    }
}
