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

class PropData extends Backend
{
    //模型对象
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\by\PropData;
    }

    /**
     * 实时数据
     */
    public function current()
    {
        if($this->request->isAjax()){
            $params = ['game_info' => $this->gameInfo];
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['Time'])){
                $where['Time'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['Time'][0]);
            }else{
                // 默认查询当天数据
                $where['Time'][0] = '>=';
                $where['Time'][1] = date('Y-m-d');
            }

            $params['where'] = $where;
            $params['sort'] = $sort;
            $params['order'] = $order;
            $params['offset'] = $offset;
            $params['limit'] = $limit;
            //halt($params);exit;
            $result = $this->model->getList($params);
            return json($result);
        }

        return $this->fetch();
    }

    /**
     * 详细数据
     */
    public function detail()
    {
        $this->searchFields = 'GivePID,GiveNickName';
        if($this->request->isAjax()){
            $params = ['game_info' => $this->gameInfo];
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            // 时间特殊处理
            if(!empty($where['AddTime'])){
                $where['AddTime'][0] = str_replace('BETWEEN time', 'BETWEEN', $where['AddTime'][0]);
            }else{
                // 默认查询当天数据
                $where['AddTime'][0] = '>=';
                $where['AddTime'][1] = date('Y-m-d');
            }
            //halt($where);
            $params['where'] = $where;
            $params['sort'] = $sort;
            $params['order'] = $order;
            $params['offset'] = $offset;
            $params['limit'] = $limit;
            $result = $this->model->getPropDetails($params);
            return json($result);
        }
        return $this->fetch();
    }
}
