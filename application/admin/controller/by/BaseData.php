<?php
/**
 * 基础统计
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/8/30
 */
namespace app\admin\controller\by;

use app\common\controller\Backend;

class BaseData extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\by\BaseData;
    }

    /**
     * 统计汇总
     */
    public function summary()
    {
        if ($this->request->isAjax()) {
            $param = ['gameInfo' => $this->gameInfo, 'limit' => $this->request->get("limit", 2)];
            $result = $this->model->getSummary($param);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 实时数据
     */
    public function realtime()
    {
        if ($this->request->isAjax()) {
            $param = ['gameInfo' => $this->gameInfo];
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['c_t_time'])){
                $where['c_t_time'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['c_t_time'][0]);
            }else{
                // 默认查询当天数据
                $where['c_t_time'][0] = '>=';
                $where['c_t_time'][1] = date('Y-m-d');
            }
            $param['where'] = $where;
            $param['offset'] = $offset;
            $param['limit'] = $limit;
            $result = $this->model->getRealTime($param);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 付费排行
     */
    public function payrank()
    {
        if ($this->request->isAjax()) {
            $param = ['gameInfo' => $this->gameInfo];
            $result = $this->model->getPayRank($param);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 存量数值
     */
    public function stockvalue()
    {
        if ($this->request->isAjax()) {
            $param = ['gameInfo' => $this->gameInfo];
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['Time'])){
                $where['Time'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['Time'][0]);
            }
            $param['where'] = $where;
            $param['offset'] = $offset;
            $param['limit'] = $limit;
            $result = $this->model->getStockValue($param);
            return json($result);
        }
        return $this->view->fetch();
    }
}
