<?php
/**
 * 运营数据
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/18
 */
namespace app\admin\controller\by;
use app\common\controller\Backend;
class OperateData extends Backend
{
    protected $model = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\by\OperateData;
    }
    /**
     * 留存数据
     */
    public function remain()
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
            $result = $this->model->getRemain($param);
            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 渠道数据
     */
    public function channel()
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
            $result = $this->model->getChannel($param);
            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 付费行为
     */
    public function payment()
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
            $result = $this->model->getPayment($param);
            return json($result);
        }
        return $this->view->fetch();
    }
}
