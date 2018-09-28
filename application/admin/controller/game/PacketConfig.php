<?php
/**
 * 红包配置
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\game;

use app\common\controller\Backend;

class PacketConfig extends Backend
{
    /**
     * @var app\admin\model\game\PacketConfig
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\game\PacketConfig;
        $this->assign('statusList', $this->model->getStatusList());
        $this->assign('typeList', $this->model->getTypeList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 重置配置
     */
    public function reset()
    {
        if($this->request->isAjax()){
            if(empty($this->gameInfo['id'])){
                return json(['code'=>0, 'msg'=>'游戏参数错误，请选择游戏']);
            }

            $module = 'exchange';
            $action = 'packetconfig';

            $result = $this->resetGameServerData($module, $action, [],true);
            return json($result);
        }
    }
}