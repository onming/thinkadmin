<?php
/**
 * 基础统计model
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/17
 */
namespace app\admin\model\by;

use think\Model;
use think\Db;

class BaseData extends Model
{

    /**
     * 获取汇总数据
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getSummary($param)
    {
        $model = Db::connect($param['gameInfo']['mysql_game_log_config']);
        $sql = " CALL Pro_Statistic_TotalData('2018-08-01', '" . date('Y-m-d H:i:s') . "')";
        $list[] = $model->query($sql)[0][0];
        $sql = " CALL Pro_Statistic_TotalData('" . date('Y-m-d') . "', '" . date('Y-m-d H:i:s') . "')";
        $list[] = $model->query($sql)[0][0];
        for($i=1;$i<=$param['limit'];$i++){
            $sql = " CALL Pro_Statistic_TotalData('" . date('Y-m-d', strtotime("-$i day")) . "', '" . date('Y-m-d 23:59:59', strtotime("-$i day")) . "')";
            $list[] = $model->query($sql)[0][0];
        }
        foreach ($list as $key => &$value) {
            $value['averageOnline'] = $value['ActiveUserNum'] == 0 ? 0 : round(($value['TotalOnline'] / $value['ActiveUserNum']), 1);
            $value['NewUserAvgOnline'] = $value['NewRegister'] == 0 ? 0 : round(($value['NewUserTotalOnline'] / $value['NewRegister']), 1);
            $value['NewUserArpu'] = $value['NewRegister'] == 0 ? 0 : round(($value['PayAmount'] / $value['NewRegister']), 1);
        }
        //累积
        $list[0]['Time'] = '累积';
        $list[0]['ActiveUserNum'] = $list[0]['NewRegister'];
        $list[0]['NewRegister'] = '-';
        $list[0]['NewUserAvgOnline'] = '-';
        $list[0]['NewUserArpu'] = $list[0]['NewUserArpu'];
        return ["total" => 0, "rows" => $list];
    }

    /**
     * 获取实时数据
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getRealTime($param)
    {
        $model = BaseData::connect($param['gameInfo']['mysql_log_config']);
        $total = $model->table('b_d_current_time')->where($param['where'])->group('c_t_time')->count();
        // 列表
        $select = "c_t_time,
                SUM(c_t_new_user_num) as c_t_new_user_num,
                SUM(c_t_online_user_num) as c_t_online_user_num,
                SUM(c_t_room_user_num) as c_t_room_user_num,
                SUM(c_t_pay_user_num) as c_t_pay_user_num,
                SUM(c_t_pay_num) as c_t_pay_num,
                SUM(c_t_pay_amount) as c_t_pay_amount,
                (SUM(c_t_lottery_output/200)) as c_t_lottery_output,
                (SUM(c_t_lottery_consume/200)) as c_t_lottery_consume,
                (SUM(c_t_prop_output/10000)) as c_t_prop_output,
                (SUM(c_t_prop_use/10000)) as c_t_prop_use,
                (SUM(c_t_packet_output/100)) as c_t_packet_output,
                (SUM(c_t_packet_exchange/100)) as c_t_packet_exchange";

        $list = $model->table('b_d_current_time')
            ->where($param['where'])
            ->group('c_t_time')
            ->order('c_t_time DESC')
            ->field($select)
            ->select();

        $list = collection($list)->toArray();
        // 统计在线总时长&最高在线人数
        $total_online_user_num = 0;
        $pcu = 0;
        foreach ((array)$list as $row){
            if($row['c_t_online_user_num'] > $pcu){
                $pcu = $row['c_t_online_user_num'];
            }
            $total_online_user_num += $row['c_t_online_user_num'];
        }
        // 计算在线平均时长
        $acu = !empty($total_online_user_num)?round(($total_online_user_num/$total), 2):0;
        //转换成图表使用的数据
        $echarts_data = format_echarts_data($list);
        // 统计总数
        $top_total = [
            'c_t_time' => '合计('.$total.'条)',
            'c_t_pay_user_num' => !empty($echarts_data['c_t_pay_user_num'])?array_sum($echarts_data['c_t_pay_user_num']):0,
            'c_t_pay_num' => !empty($echarts_data['c_t_pay_num'])?array_sum($echarts_data['c_t_pay_num']):0,
            'c_t_pay_amount' => !empty($echarts_data['c_t_pay_amount'])?array_sum($echarts_data['c_t_pay_amount']):0,
            'c_t_new_user_num' => !empty($echarts_data['c_t_new_user_num'])?array_sum($echarts_data['c_t_new_user_num']):0,
            'c_t_online_user_num' => '-',
            'c_t_room_user_num' => '-',
            'c_t_lottery_output' => !empty($echarts_data['c_t_lottery_output'])?array_sum($echarts_data['c_t_lottery_output']):0,
            'c_t_lottery_consume' => !empty($echarts_data['c_t_lottery_consume'])?array_sum($echarts_data['c_t_lottery_consume']):0,
            'c_t_prop_output' => !empty($echarts_data['c_t_prop_output'])?array_sum($echarts_data['c_t_prop_output']):0,
            'c_t_prop_use' => !empty($echarts_data['c_t_prop_use'])?array_sum($echarts_data['c_t_prop_use']):0,
            'c_t_packet_output' => !empty($echarts_data['c_t_packet_output'])?array_sum($echarts_data['c_t_packet_output']):0,
            'c_t_packet_exchange' => !empty($echarts_data['c_t_packet_exchange'])?array_sum($echarts_data['c_t_packet_exchange']):0,
        ];
        // @todo 目前需求是取时间区所有数据无法采用分页后期需优化
        $list = array_slice($list, $param['offset'], $param['limit']);
        array_unshift($list, $top_total);
        return ["total" => $total, "rows" => $list, "extend" => ['pcu' => $pcu, 'acu' => $acu, 'echarts_data' => $echarts_data]];
    }

    /**
     * 获取付费排行
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getPayRank($param)
    {
        $sql = "SELECT  p.PID AS userId
                    , p.PlayerName AS playerName
                    , (SELECT IFNUlL(SUM(Amout), 0) FROM payorder WHERE PID = p.PID AND GameId = 5 AND (PayStatus = 3 OR PayStatus = 4)) AS amount
                    , p.CreateTime AS firstTime
                    , (SELECT CreateTime From payorder WHERE PID = p.PID AND GameId = 5 ORDER BY CreateTime DESC LIMIT 1) AS lastTime
                    , (SELECT COUNT(1) FROM payorder WHERE PID = p.PID AND GameId = 5 AND (PayStatus = 3 OR PayStatus = 4)) AS payNum
                    , (SELECT IFNULL(SUM(login.diff), 0) FROM (SELECT PID, TIMESTAMPDIFF(SECOND , LoginTime, LogoutTime) %(24*3600)/3600 as diff FROM TrackLogInLog) login WHERE login.PID = p.PID) AS onlineHour
                    , p.PlatForm AS channelKey
            FROM payorder p 
            WHERE 1 = 1
                AND GameId = 5
                AND (PayStatus = 3 OR PayStatus = 4)
            GROUP BY p.PID
            ORDER BY Amount DESC LIMIT 100";
        $list = Db::connect($param['gameInfo']['mysql_game_log_config'])->query($sql);
        $total = count($list);
        $list = collection($list)->toArray();
        return ["total" => $total, "rows" => $list];
    }

    /**
     * 获取存量数值
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getStockValue($param)
    {
        $model = BaseData::connect($param['gameInfo']['mysql_log_config']);
        $total = $model->table('b_d_base_stock_value')->where($param['where'])->group('Time')->count();
        $select = "Time as Time
            , sum(GoldStock) as GoldStock
            , sum(ActiveGoldStock) as ActiveGoldStock
            , sum(PropStock) as PropStock
            , sum(ActivePropStock) as ActivePropStock
            , sum(NewGiftGoldStock) as NewGiftGoldStock
            , sum(PayAmount) as PayAmount
            , sum(RedBagAmount) as RedBagAmount
            , sum(LotteryAmount) as LotteryAmount";

        $list = $model->table('b_d_base_stock_value')
            ->where($param['where'])
            ->group('Time')
            ->order('Time DESC')
            ->limit($param['offset'], $param['limit'])
            ->field($select)
            ->select();

        // 计算存量日差值
        foreach ($list as $key => $value) {
            $oneDayBefore = date('Y-m-d', strtotime($value['Time']) - 24*60*60);
            // 平台特殊统计
            if(!empty($param['where']['PlatForm'])){
                $_where = ['Time'=> $oneDayBefore, 'PlatForm' => $param['where']['PlatForm']];
            }else{
                $_where = ['Time'=> $oneDayBefore];
            }
            $oneDayBeforeGoldStock = $model->table('b_d_base_stock_value')->where($_where)->value('sum(GoldStock) as GoldStock');
            $list[$key]['GoldStockDiff'] = $value['GoldStock'] - $oneDayBeforeGoldStock;
            //  换算RMB
            $list[$key]['GoldStockDiff'] /= 10000;
            $list[$key]['GoldStock'] /= 10000;
            $list[$key]['ActiveGoldStock'] /= 10000;
            $list[$key]['PropStock'] /= 10000;
            $list[$key]['ActivePropStock'] /= 10000;
            $list[$key]['NewGiftGoldStock'] /= 10000;
            $list[$key]['RedBagAmount'] /= 10000;
            $list[$key]['LotteryAmount'] /= 10000;
        }
        return ["total" => $total, "rows" => $list];
    }
}