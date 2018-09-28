<?php
/**
 * 排行榜
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2016/6/30
 */
namespace app\admin\controller\ql;

use app\common\controller\Backend;
use think\Db;

class RankList extends Backend
{

    /**
     * 盈利榜
     */
    public function profit()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            $model = Db::connect($this->gameInfo['mysql_read_config']);
            $total = $model->table('view_rankshell')->count();
            $list = $model->table('view_rankshell')
                ->limit($offset, $limit)
                ->select();
            foreach ((array)$list as $k=>$v)
            {
                // 计算排名
                $v['ranking'] = $offset+$k+1;
                $list[$k] = $v;
            }
            return json(["total" => $total, "rows" => $list]);
        }
        return $this->view->fetch();
    }

    /**
     * 财富榜
     */
    public function wealth()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            $model = Db::connect($this->gameInfo['mysql_read_config']);
            $total = $model->table('view_rankgold')->count();
            $list = $model->table('view_rankgold')
                ->limit($offset, $limit)
                ->select();
            foreach ((array)$list as $k=>$v)
            {
                // 计算排名
                $v['ranking'] = $offset+$k+1;
                $list[$k] = $v;
            }
            return json(["total" => $total, "rows" => $list]);
        }
        return $this->view->fetch();
    }

    /**
     * 钻石榜
     */
    public function diamond()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams(null, null, false);
            $model = Db::connect($this->gameInfo['mysql_read_config']);
            $total = $model->table('view_rankdiamond')->count();
            $list = $model->table('view_rankdiamond')
                ->limit($offset, $limit)
                ->select();
            foreach ((array)$list as $k=>$v)
            {
                // 计算排名
                $v['ranking'] = $offset+$k+1;
                $list[$k] = $v;
            }
            return json(["total" => $total, "rows" => $list]);
        }
        return $this->view->fetch();
    }

}