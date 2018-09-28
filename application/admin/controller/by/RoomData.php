<?php
/**
 * 道具统计
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/19
 */
namespace app\admin\controller\by;

use app\common\controller\Backend;

class RoomData extends Backend
{
    //模型对象
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\by\RoomData;
    }

    /**
     * 实时数据
     */
    public function current()
    {
        if($this->request->isAjax()){
            $params = ['game_info' => $this->gameInfo];

            //调用存储过程，不能使用连贯操作
            $filter = json_decode(input('get.filter', '{}'), true);
            $where = [];
            $where['platform'] = '';
            $where['start_time'] = date('Y-m-d');
            $where['end_time'] = date('Y-m-d', time()+24*60*60);
            if(!empty($filter)){
                if(isset($filter['Time'])){
                    $times = explode(' - ', $filter['Time']);
                    $where['start_time'] = $times[0];
                    $where['end_time'] = $times[1];
                }
                if(isset($filter['PlatForm'])){
                    $where['platform'] = $filter['PlatForm'];
                }
            }

            $params['where'] = $where;
            $params['offset'] = intval(input('get.offset', 0));
            $params['limit'] = intval(input('get.limit', 10));
            //halt($params);
            $result = $this->model->getCurrentData($params);
            return json($result);
        }

        return $this->fetch();
    }

    /**
     * 详细数据
     */
    public function arena()
    {
        $this->searchFields = 'GivePID,GiveNickName';
        if($this->request->isAjax()){
            $params = ['game_info' => $this->gameInfo];
            //调用存储过程，不能使用连贯操作
            $filter = json_decode(input('get.filter', '{}'), true);
            $where = [];
            $where['platform'] = '';
            $where['start_time'] = date('Y-m-d');
            $where['end_time'] = date('Y-m-d', time()+24*60*60);
            if(!empty($filter)){
                if(isset($filter['Time'])){
                    $times = explode(' - ', $filter['Time']);
                    $where['start_time'] = $times[0];
                    $where['end_time'] = $times[1];
                }
                if(isset($filter['PlatForm'])){
                    $where['platform'] = $filter['PlatForm'];
                }
            }

            $params['where'] = $where;
            $params['offset'] = intval(input('get.offset', 0));
            $params['limit'] = intval(input('get.limit', 10));
            $result = $this->model->getArenaData($params);
            return json($result);
        }
        return $this->fetch();
    }
}
