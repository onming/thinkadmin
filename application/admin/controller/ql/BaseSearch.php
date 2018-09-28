<?php
/**
 * 基础查询
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/08/30
 */
namespace app\admin\controller\ql;

use app\common\controller\Backend;
use think\Db;

class BaseSearch extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 玩家对战场次 斗地主
     */
    public function playWarNum()
    {
        if ($this->request->isAjax()) {
            $param = ['gameInfo' => $this->gameInfo];
            $data = input('get.');
            $gameId = $this->request->request('gameId');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['AddTime'])){
                $where['AddTime'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['AddTime'][0]);
                $start_time = $where['AddTime'][1][0];
                $end_time = $where['AddTime'][1][1];
            }else{
                $start_time = '';
                $end_time = '';
            }
            $PID = isset($where['PID']) ? $where['PID'][1] : 0;
            if(!empty($where)){
                $model = Db::connect($param['gameInfo']['mysql_read_config']);
                switch ($gameId)
                {
                    case 16:
                        $list = $model->query(" CALL Pro_TrackCrmRoomCount('".$gameId."', '" . $PID . "','" . $start_time . "', '" . $end_time . "');")[0];
                        break;
                    case 32:
                        $list = $model->query(" CALL Pro_TrackCrmRoomNNCount('".$gameId."', '" . $PID . "','" . $start_time . "', '" . $end_time . "');")[0];
                        break;
                    case 33:
                        $list = $model->query(" CALL Pro_TrackCrmRoomZJHCount('".$gameId."', '" . $PID . "','" . $start_time . "', '" . $end_time . "');")[0];
                }
                return json(["total" => count($list), "rows" => $list]);
            }
        }
        return $this->view->fetch();
    }
    /**
     * 机器人金币产出
     */
    public function robotGold()
    {
        return $this->view->fetch();
    }


}
