<?php
/**
 * 道具模型model
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/19
 */
namespace app\admin\model\by;

use think\Db;
use think\Model;

class PropData extends Model
{
    public function getList($params)
    {
        $model = Db::connect($params['game_info']['mysql_log_config']);

        //总记录
        $total = $model->table('b_d_prop_current_time')->where($params['where'])->group('Time')->count();

        //统计字段
        $field = "Time,any_value(PlatForm) PlatForm,
                SUM(PropSaled/10000) AS PropSaled,
                SUM(PropGift/10000) AS PropGift,
                SUM(PropOutput/10000) AS PropOutput,
                SUM(RedBagOutput/100) AS RedBagOutput,
                SUM(CrystalWhaleOutput) AS CrystalWhaleOutput,
                SUM(CrystalHunterOutput) AS CrystalHunterOutput,
                SUM(CrystalShopOutput) AS CrystalShopOutput";

        $list = $model->table('b_d_prop_current_time')
            ->field($field)
            ->where($params['where'])
            ->order('Time', 'desc')
            //->limit($params['offset'], $params['limit'])
            ->group('Time')
            ->select();
        //dump($model->getLastSql());exit;

        //halt($list);
        array_walk($list, function (&$value){
            $value['CrystalOutput'] = $value['CrystalWhaleOutput'] + $value['CrystalWhaleOutput'] + $value['CrystalShopOutput'];
        });

        //转换成图表使用的数据
        $echartsData = (object)[];
        if($list){
            $newList = $list;
            array_multisort(array_column($newList, 'Time'), SORT_ASC, $newList);
            $echartsData = format_echarts_data($newList);

            $arrTopSum = [
                'Time' => '合计('.$total.'条)',
                'PropSaled' => array_sum($echartsData['PropSaled']),
                'PropGift' => array_sum($echartsData['PropGift']),
                'PropOutput' => array_sum($echartsData['PropOutput']),
                'RedBagOutput' => array_sum($echartsData['RedBagOutput']),
                'CrystalOutput' => array_sum($echartsData['CrystalOutput'])
            ];

            $list = array_slice($list, $params['offset'], $params['limit']);

            array_unshift($list, $arrTopSum);
        }

        return ['total'=>$total, 'rows'=>$list, 'extend'=>['echarts_data'=>$echartsData]];
    }

    public function getPropDetails($params)
    {
        $model = Db::connect($params['game_info']['mysql_game_log_config']);
        $where = $params['where'];
        $where['GameId'] = $params['game_info']['game_id'];
        $where['PlatForm'] = ['neq', ''];
        if(is_string($where['AddTime'][1])){
            $starYear = date('Y', strtotime($where['AddTime'][1]));
            $starMonth = date('m', strtotime($where['AddTime'][1]));
            $endYear = date('Y', strtotime($where['AddTime'][1]));
            $endMonth = date('m', strtotime($where['AddTime'][1]));
        }

        if(is_array($where['AddTime'][1])){
            $starYear = date('Y', strtotime($where['AddTime'][1][0]));
            $starMonth = date('m', strtotime($where['AddTime'][1][0]));
            $endYear = date('Y', strtotime($where['AddTime'][1][1]));
            $endMonth = date('m', strtotime($where['AddTime'][1][1]));
        }

        $unionSql = '';
        $unionCountSql = '';
        for($i = $starYear; $i <= $endYear; $i++) {
            for($j = $starMonth; $j < $endMonth; $j++) {

                $_suffix = "{$i}{$j}";
                $subTable = 'TrackCrmFriendGivePropLog_'.$_suffix;
                $subAlias = 'fg_'.$_suffix;
                $result = Db::query('show tables like "'.$subTable.'"');
                if(!$result){
                    continue;
                }
                $unionSql .= " UNION SELECT  *,(SELECT IFNULL(SUM(Amout), 0) FROM payorder WHERE PID = {$subAlias}.GivePID AND OrderStatus = 1 AND GameId = 5) AS Amount,
                                      (SELECT IFNULL(COUNT(1), 0) FROM {$subTable} WHERE GivePID = {$subAlias}.GivePID) AS GivedNum 
                                      FROM {$subTable} {$subAlias}";
                $unionCountSql .= "UNION SELECT count(*) AS tp_count
                                      FROM {$subTable} {$subAlias}";
            }
        }

        $countSql = $model->table('TrackCrmFriendGivePropLog fg')
            ->where($where)
            ->fetchSql(true)
            ->count();

        $query = $model->table('TrackCrmFriendGivePropLog fg')
            ->where($where)
            ->order($params['sort'], $params['order'])
            ->limit($params['offset'], $params['limit'])
            ->fetchSql(true)
            ->select();
        $subQuery = ",(SELECT IFNULL(SUM(Amout), 0) FROM payorder WHERE PID = fg.GivePID AND OrderStatus = 1 AND GameId = 5) AS Amount,(SELECT IFNULL(COUNT(1), 0) FROM TrackCrmFriendGivePropLog WHERE GivePID = fg.GivePID) AS GivedNum";
        $sql = substr_replace($query, $subQuery, strpos($query, '*')+1, 0);
        if(!empty($unionSql)){
            $sql = substr_replace($sql, $unionSql, strrpos($sql, 'WHERE')-1, 0);
            $countSql = substr_replace($countSql, $unionCountSql, strrpos($countSql, 'WHERE')-1, 0);
        }

        $total = $model->query($countSql)[0]['tp_count'];
        $list = $model->query($sql);

        return ["total" => $total, "rows" => $list];


    }


}