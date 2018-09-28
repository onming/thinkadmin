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

class RoomData extends Model
{
    public function getCurrentData($params)
    {
        $model = Db::connect($params['game_info']['mysql_game_log_config']);

        $where = $params['where'];

        //调用存储过程，没有分页功能，待优化
        $list = $model->query("CALL Pro_Statistic_RoomData(:platform, :start_time, :end_time)", ['platform'=>$where['platform'], 'start_time'=>$where['start_time'], 'end_time'=>$where['end_time']]);

        !isset($list[0][0]) ?: $list = $list[0];
        //dump($model->getLastSql());exit;
        //halt($list);

        @array_walk($list, function (&$value){
            $value['GoldValue'] = $value['GoldValue'] / 10000 * (-1);
            $value['NewOutGoldValue'] = $value['NewOutGoldValue'] / 10000 * (-1);
            $value['PropValue'] = $value['PropValue'] / 10000 * (-1);
            $value['TicketValue'] = $value['TicketValue'] / 200 * (-1);
            $value['RedBagValue'] = $value['RedBagValue'] / 10000 * (-1);
            $value['totalProfitLoss'] = (int)$value['PlayerNum'] == 0 ? '0' : round($value['GoldValue'] / $value['PlayerNum'], 2);
            $value['newPlayerProfitLoss'] = (int)$value['NewPlayerNum'] == 0 ? '0' : round($value['NewOutGoldValue'] / $value['NewPlayerNum'], 2);
            $value['TotalOnline'] = round($value['TotalOnline'] / $value['PlayerNum'], 2);

            switch ($value['RoomType']) {
                case '潜水遗迹':
                    $value['Id'] = '01';
                    $value['RoomType'] .= '（初级场）';
                    break;
                case '冰河探险':
                    $value['Id'] = '02';
                    $value['RoomType'] .= '（中级场）';
                    break;
                case '月色海滩':
                    $value['Id'] = '03';
                    $value['RoomType'] .= '（高级场）';
                    break;
                case '深海绝域':
                    $value['Id'] = '04';
                    $value['RoomType'] .= '（特级场）';
                    break;
            }
        });

        $total = count($list);
        //转换成图表使用的数据
        $echartsData = (object)[];
        if($list){
            $echartsData = format_echarts_data($list);

            $arrTopSum = [
                'RoomType' => '合计('.$total.'条)',
                'GoldValue' => array_sum($echartsData['GoldValue']),
                'PlayerNum' => array_sum($echartsData['PlayerNum']),
                'IsOnline' => array_sum($echartsData['IsOnline']),
                'InRoomNum' => array_sum($echartsData['InRoomNum']),
                'NewOutGoldValue' => array_sum($echartsData['NewOutGoldValue']),
                'PropValue' => array_sum($echartsData['PropValue']),
                'TicketValue' => array_sum($echartsData['TicketValue']),
                'RedBagValue' => array_sum($echartsData['RedBagValue']),
                'totalProfitLoss' => array_sum($echartsData['totalProfitLoss']),
                'newPlayerProfitLoss' => array_sum($echartsData['newPlayerProfitLoss']),
                'TotalOnline' => array_sum($echartsData['TotalOnline']),
            ];

            $list = @array_slice($list, $params['offset'], $params['limit']);

            array_unshift($list, $arrTopSum);
        }

        return ['total'=>$total, 'rows'=>$list, 'extend'=>['echarts_data'=>$echartsData]];
    }

    public function getArenaData($params)
    {
        $model = Db::connect($params['game_info']['mysql_game_log_config']);

        $where = $params['where'];

        //调用存储过程，没有分页功能，待优化
        $list = $model->query("CALL Pro_Statistic_ArenaData(:platform, :start_time, :end_time)", ['platform'=>$where['platform'], 'start_time'=>$where['start_time'], 'end_time'=>$where['end_time']]);

        //halt($model->getLastSql());
        //halt($list);

        !isset($list[0][0]) ?: $list = $list[0];

        @array_walk($list, function (&$value){
            $value['ArenaEntryFeeValue'] = $value['ArenaEntryFeeValue'] / 10000;
            $value['ArenaoutRewardValue'] = $value['ArenaoutRewardValue'] / 10000;
            $value['ProfitLoss'] = $value['ArenaEntryFeeValue'] - $value['ArenaoutRewardValue'];
        });

        $totalEnroll = array_sum(array_column($list, 'ArenaEntryFeeValue'));
        $activeNums = array_sum(array_column($list, 'ActivePlayerNum'));
        $totalProfix = array_sum(array_column($list, 'ProfitLoss'));
        $extend = [
            'total_enroll' => sprintf('%.3f', $totalEnroll),
            'active_nums' => $activeNums,
            'total_profix' => sprintf('%.3f', $totalProfix),
        ];

        $total = count($list);

        $list = @array_slice($list, $params['offset'], $params['limit']);

        return ["total" => $total, "rows" => $list, "extend"=>$extend];


    }


}