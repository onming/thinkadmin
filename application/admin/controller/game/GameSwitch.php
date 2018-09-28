<?php
/**
 * 游戏开关
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/10
 */
namespace app\admin\controller\game;

use app\common\controller\Backend;
use fast\Tree;
use think\Hook;

class GameSwitch extends Backend
{
    /**
     * Game模型对象
     * @var \app\admin\model\game\GameSwitch
     */
    protected $model = null;

    protected $arrChannel = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\game\GameSwitch;

        if(empty($this->gameInfo['game_id'])){
            $this->error(__('Choose game'));
        }

        $game_id = $this->gameInfo['game_id'];

        //渠道列表
        list($arrChannel, $channelList) = $this->model->getChannelList($game_id);
        $tree = Tree::instance();
        $tree->init($arrChannel, 'pid');
        $this->arrChannel = $arrChannel = $tree->getTreeList($tree->getTreeArray(0), 'channel_name');

        //dump($arrVip);exit;
        $action = $this->request->action();
        if(in_array($action, ['add', 'edit'])){

            if($action == 'add'){
                //场景列表
                $arrScene = $this->model->getSceneList($game_id);
                $this->assign('arrScene', $arrScene);
            }

            //vip列表
            $arrVip = $this->model->getVipList();
            $this->assign('arrChannel', $arrChannel);
            $this->assign('arrVip', $arrVip);
        }

        $this->assignconfig('init_channle_id', $channelList[0]['id']);
        $this->assignconfig('channelList', $channelList);
        $this->assign('statusList', $this->model->getStatusList());
    }

    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if($this->request->isAjax()){
            //$this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with([
                    'channel' => function($query) {
                        $query->withField('channel_name');
                    },
                    'scene' => function($query){
                        $query->withField('scene_name');
                    }
                ])
                ->where($where)
                ->order($sort, $order)
                ->count();
            //halt($total);

            $list = $this->model
                ->with([
                    'channel' => function($query) {
                        $query->withField('id,channel_name');
                    },
                    'scene' => function($query){
                        $query->withField('id,scene_name');
                    },
                    ])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                // 是否自动填充game
                if($this->gameAuto) {
                    $params['game_id'] = $this->gameInfo['game_id'];
                }
                try {
                    //参数整合
                    $params = $this->setParams($params);
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 修改
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //参数整合
                    $params = $this->setParams($params);
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }


        //halt($row->toArray());
        $arrScene = $this->model->getSceneList($this->gameInfo['game_id'], $row['channel_id'], $row['scene_id']);
        $this->view->assign('arrScene', $arrScene);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 开关同步
     * @return mixed
     */
    public function sync()
    {
        if(empty($this->gameInfo['game_id'])){
            $this->error(__('Choose game'));
        }
        $params = [
            'game_id' => $this->gameInfo['game_id'],
        ];
        $result = Hook::listen('switch_sync', $params, '', true);

        return $result;
    }

    /**
     * 重置数据
     */
    public function reset()
    {
        if($this->request->isAjax()){
            if(empty($this->gameInfo['id'])){
                return json(['code'=>0, 'msg'=>'游戏参数错误，请选择游戏']);
            }

            $module = 'game';
            $action = 'gameswitch';

            $result = $this->resetGameServerData($module, $action, [], true);
            return json($result);
        }
    }

    /**
     * 参数整合
     */
    protected function setParams($params)
    {
        $tree = Tree::instance();
        $tree->init($this->arrChannel, 'pid');
        $arrChannelParents = $tree->getParents($params['channel_id']);
        foreach ($arrChannelParents as $value){
            if($value['pid'] == 0){
                $params['channel_id'] = $value['id'];
                break;
            }
        }

        if(!isset($params['timespace'])){
            $params['timespace'] = [];
        }

        array_multisort(array_column($params['timespace'], 'start_time'), SORT_ASC, $params['timespace']);
        $params['timespace'] = json_encode($params['timespace']);

        return $params;
    }


    /**
     * ajax 根据渠道获取场景
     */
    public function ajaxGetScene()
    {
        if($this->request->isAjax()){
            $channel_id = input('get.channel_id');
            if(empty($channel_id)){
                return json(['codel'=>0, 'msg'=>__('Empty channel id')]);
            }

            //顶级channel_id
            $tree = Tree::instance();
            $tree->init($this->arrChannel, 'pid');
            $arrChannelParents = $tree->getParents($channel_id);
            foreach ($arrChannelParents as $value){
                if($value['pid'] == 0){
                    $channel_id = $value['id'];
                    break;
                }
            }

            $arrScene = $this->model->getSceneList($this->gameInfo['game_id'], $channel_id);

            if(!$arrScene){
                return json(['codel'=>0, 'msg'=>__('Scene not exists')]);
            }
            return json(['code'=>1, 'msg'=>__('Success'), 'data'=>$arrScene]);
        }
    }



}