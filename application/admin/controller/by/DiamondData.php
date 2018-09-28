<?php
/**
 * 水晶数据
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2016/6/30
 */
namespace app\admin\controller\by;

use app\common\controller\Backend;
use think\Db;

class DiamondData extends Backend
{
    protected $searchFields = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 钻石交易记录
     */
    public function tradeList()
    {
        $this->searchFields = 'PID,NickName';
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['AddTime'])){
                $where['AddTime'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['AddTime'][0]);
            }
            $model = Db::connect($this->gameInfo['mysql_game_log_config']);
            $total = $model->table('TrackCrmDiamondLog')->where($where)->count();
            $list = $model->table('TrackCrmDiamondLog')
                ->where($where)
                ->order('AddTime DESC')
                ->limit($offset, $limit)
                ->select();
            // 统计获得消耗
            $huode = $model->table('TrackCrmDiamondLog')->where($where)->where('DiamondActionType', '1')->sum('DiamondNum');
            $xiaohao = $model->table('TrackCrmDiamondLog')->where($where)->where('DiamondActionType', '-1')->sum('DiamondNum');
            return json(["total" => $total, "rows" => $list, "extend" => ['count' => $total, 'huode' => $huode, 'xiaohao' => $xiaohao]]);
        }
        return $this->view->fetch();
    }

    /**
     * 钻石交易统计
     */
    public function tradeCount()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['Time'])){
                $where['Time'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['Time'][0]);
            }
            $model = Db::connect($this->gameInfo['mysql_log_config']);
            $total = $model->table('b_d_prop_current_time')->where($where)->group('Time')->count();
            $list = $model->table('b_d_prop_current_time')
                ->where($where)
                ->order('Time DESC')
                ->limit($offset, $limit)
                ->select();

            return json(["total" => $total, "rows" => $list]);
        }
        return $this->view->fetch();
    }

}
