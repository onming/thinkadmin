<?php
/**
 * 渠道管理
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\game;

use app\common\controller\Backend;
use fast\Tree;
use think\Hook;

class Channel extends Backend
{
    
    /**
     * Channel模型对象
     * @var \app\admin\model\game\Channel
     */
    protected $model = null;

    /**
     * @var array 渠道列表
     */
    protected $channelList = [];

    /**
     * @var array 操作判断
     */
    protected $actionFlag = ['add'];

    public function _initialize()
    {
        parent::_initialize();
        $this->gameAuto = true;
        $this->model = new \app\admin\model\game\Channel;

        //判断当前操作
        /*$action = $this->request->action();
        if(in_array($action, $this->actionFlag)){

        }*/
        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->channelList = $tree->getTreeList($tree->getTreeArray(0), 'channel_name');
        $channelData = [0 => ['channel_name' => __('None')]];
        foreach ($this->channelList as $k => $v)
        {
            $channelData[$v['id']] = $v;
        }
        $this->view->assign("channelData", $channelData);

        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 重写index
     */
    public function index()
    {
        //设置过滤方法
//        $this->request->filter(['strip_tags']);
        if($this->request->isAjax()){
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->where($where)
                ->where('game_id', '=', $this->gameInfo['game_id'])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('game_id', '=', $this->gameInfo['game_id'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $tree = Tree::instance();
            $tree->init(collection($list)->toArray(), 'pid');
            $list = $tree->getTreeList($tree->getTreeArray(0), 'channel_name');

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->fetch();
    }
    
}
