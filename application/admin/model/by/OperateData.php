<?php
/**
 * 运营数据统计model
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/9/19
 */
namespace app\admin\model\by;
use think\Model;
use think\Db;
class OperateData extends Model
{
    /**
     * 获取留存数据
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getRemain($param) {
        $model = Db::connect($param['gameInfo']['mysql_log_config']);
        $total = $model->table('b_d_operational_retain_value')->where($param['where'])->group('Time')->count();
        // 列表
        $select = "any_value(id) as id,
                any_value(Time) as Time,
                any_value(PlatForm) as PlatForm,
                SUM(NewPlayerNum) as NewPlayerNum,
                SUM(ActivePlayerNuam) as ActivePlayerNuam,
                SUM(PayMoney) as PayMoney,
                any_value(CrKeep) as CrKeep,
                any_value(SrKeep) as SrKeep,
                any_value(QrKeep) as QrKeep,
                any_value(SWrKeep) as SWrKeep,
                any_value(AddTime) as AddTime";
        $list = $model->table('b_d_operational_retain_value')
            ->where($param['where'])
            ->group('Time')
            ->order('Time','DESC')
            ->field($select)
            ->select();
        if(empty($param['where']['PlatForm'])) {
            foreach ($list as &$value){
                $value['CrKeep'] = '-';
                $value['SrKeep'] = '-';
                $value['QrKeep'] = '-';
                $value['SWrKeep'] = '-';
            }
        }
        //转换成图表使用的数据
        $echarts_data = format_echarts_data($list);
        $list = array_slice($list, $param['offset'], $param['limit']);
        return ["total" => $total, "rows" => $list, 'echarts_data' => $echarts_data];
    }
    /**
     * 获取渠道数据
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getChannel($param) {
        $model = Db::connect($param['gameInfo']['mysql_log_config']);
        $total = $model->table('b_d_operational_channel')->where($param['where'])->group('Time')->count();
        // 列表
        $select = "any_value(id) as id,
                any_value(Time) as Time,
                any_value(PlatForm) as PlatForm,
                SUM(NewPlayerNum) as NewPlayerNum,
                SUM(ActivePlayerNuam) as ActivePlayerNuam,
                SUM(PayMoney) as PayMoney,
                SUM(NewPayMoney) as NewPayMoney,
                SUM(PayPlayerNuam) as PayPlayerNuam,
                SUM(NewPayPlayerNum) as NewPayPlayerNum";
        $returnList = $model->table('b_d_operational_channel')
            ->where($param['where'])
            ->group('Time')
            ->order('Time DESC')
            ->limit($param['offset'],$param['limit'])
            ->field($select)
            ->select();
        $list = [];
        //兑算活跃付费率、新增付费率、ARPU、ARPPU
        foreach ($returnList as $key => &$value) {
            $value['NewPayRate'] = empty($value['NewPlayerNum'])?0:(round($value['NewPayPlayerNum']/$value['NewPlayerNum'], 2)*100).'%';
            $value['ActivePayRate'] = empty($value['ActivePlayerNuam'])?0:(round($value['PayPlayerNuam']/$value['ActivePlayerNuam'], 2)*100).'%';
            empty($value['PayMoney'])?: $value['ARPU'] = empty($value['ActivePlayerNuam'])?0:round($value['PayMoney']/$value['ActivePlayerNuam'], 2);
            empty($value['PayMoney'])?: $value['ARPPU'] = empty($value['PayPlayerNuam'])?0:round($value['PayMoney']/$value['PayPlayerNuam'], 2);
            $list[] = $value;
        }
        return ["total" => $total, "rows" => $list];
    }
    /**
     * 获取付费行为数据
     *
     * @param array $param  查询参数
     * @return array
     */
    public function getPayment($param) {
        $model = Db::connect($param['gameInfo']['mysql_game_log_config']);
        $platForm = isset($param['where']['PlatForm']) ? $param['where']['PlatForm'][1] : '';
        $start_time = !isset($param['where']['Time'][1][0]) || $param['where']['Time'][1][0] == '' ? date('Y-m-d') : $param['where']['Time'][1][0];
        $end_time = !isset($param['where']['Time'][1][1]) || $param['where']['Time'][1][1] == '' ? date('Y-m-d', time() + 24*60*60) : $param['where']['Time'][1][1];
        $list = $model->query(" CALL Pro_Statistic_PayInterval('" . $platForm . "', '" . $start_time . "', '" . $end_time . "');");
        if(isset($list[0][0])){
            $data = $list[0];
            $total = count($list);
            //转换成图表使用的数据
            $echarts_data = format_echarts_data($data);
            $list = array_slice($data, $param['offset'], $param['limit']);
            $result = ["total" => $total, "rows" => $list,"echarts_data" => $echarts_data];
        }else{
            $result = ["total" => 0, "rows" => array()];
        }
        return $result;
    }
}