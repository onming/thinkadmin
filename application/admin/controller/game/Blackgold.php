<?php
/**
 * 黑金管理
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\game;

use app\common\controller\Backend;

class Blackgold extends Backend
{
    /**
     * Game模型对象
     * @var \app\admin\model\game\Blackgold
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\game\Blackgold;
        $this->assign('statusList', $this->model->getStatusList());
        $this->assign('presentList', [0 => __('Close'), 1 => __('Open')]);

        if(empty($this->gameInfo['game_id'])){
            $this->error(__('Choose game'));
        }
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 设置玩家信息
     */
    public function getUserOptions()
    {
        if($this->request->isAjax()){
            //获取用户信息
            $params = input('post.');
            $postData = [
                'module' => 'base',
                'action' => 'get_player_data',
                'timestamp' => time(),
                'data' => [
                    'player_id' => intval($params['search']),
                    'game_id'   => $this->gameInfo['game_id'],
                ]
            ];

            $result = $this->gameServerApi($postData);

            //$postData = $this->apiSign($postData);
            //dump($postData);exit;
            //$url = 'http://192.168.1.228:27002/ddz/data/api/';
            //$result = curl_post_data($url, $postData);
            //halt($result);exit;

            if(isset($result['curl_error'])){
                $data = ['code'=>0, 'msg'=>'数据异常，请稍后尝试'];
                return json($data);
            }

            if($result['code'] != 1){
                $data = ['code'=>0, 'msg'=>$result['msg'], 'data'=>[]];
                return json($data);
            }

            //vip等级
            $blackgoldList = $this->model->where('status', 1)->select();
            //跟随开关
            $followList = [
                ['id' => 0, 'name' => '跟随VIP'],
                ['id' => 1, 'name' => '关闭赠送'],
                
            ];

            //管理员权限
            $authList = [
                ['id' => 0, 'name' => '普通玩家'],
                ['id' => 1, 'name' => '普通管理员'],
                ['id' => 2, 'name' => '高级管理员'],
                ['id' => 3, 'name' => '超级管理员'],
            ];

            $this->assign('blackgold_list', $blackgoldList);
            $this->assign('follow_list', $followList);
            $this->assign('auth_list', $authList);

            $this->assign('userinfo', $result['data']);
            $data = ['code'=>1, 'msg'=>'success', 'data'=>$this->fetch()];
            return json($data);
        }
    }

    /**
     * 设置玩家vip参数等
     */
    public function setUserOptions(){
        if($this->request->isPost()){
            $playerId = input('post.player_id');
            $follow = input('post.follow');
            $blackgold = input('post.blackgold');
            $adminlv = input('post.adminlv');

            //请求数据
            $postData = [
                'module' => 'base',
                'action' => 'set_vip',
                'timestamp' => time(),
                'data' => [
                    'player_id' => $playerId,
                    'blackgold' => $blackgold,
                    'follow' => $follow,
                    'adminlv' => $adminlv,
                    'game_id' => $this->gameInfo['game_id'],
                ]
            ];

            $result = $this->gameServerApi($postData);

            if($result['code'] != 1 && !empty($result['msg'])){
                return json(['code'=>0, 'msg'=>$result['msg'], 'data'=>[]]);
            }

            return json(['code'=>1, 'msg'=>__('Operation completed'), 'data'=>[]]);
        }
    }

    /**
     * 重置服务器数据
     */
    public function reset()
    {
        if($this->request->isAjax()){
            if(empty($this->gameInfo['id'])){
                return json(['code'=>0, 'msg'=>'游戏参数错误，请选择游戏']);
            }

            $module = 'blackgold';
            $action = 'index';

            $result = $this->resetGameServerData($module, $action, [], true);
            return json($result);
        }
    }

}
