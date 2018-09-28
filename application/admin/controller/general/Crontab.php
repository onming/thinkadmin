<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;
use Cron\CronExpression;
use Think\Db;
use MongoDB\BSON\ObjectID;
/**
 * 定时任务
 *
 * @icon fa fa-tasks
 * @remark 类似于Linux的Crontab定时任务,可以按照设定的时间进行任务的执行,目前仅支持三种任务:请求URL、执行SQL、执行Shell
 */
class Crontab extends Backend
{

    protected $model = null;
    protected $conn = null;
    protected $noNeedRight = ['check_schedule', 'get_schedule_future'];

    public function _initialize()
    {
        parent::_initialize();
        $this->modelValidate = false;
        $this->model = model('Crontab');
        $this->conn = Db::connect("mongo_db")->name("crontab_log");
        $this->view->assign('typedata', \app\common\model\Crontab::getTypeList());
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => &$v)
            {
                $cron = CronExpression::factory($v['schedule']);
                $v['nexttime'] = $cron->getNextRunDate()->getTimestamp();
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 判断Crontab格式是否正确
     * @internal
     */
    public function check_schedule()
    {
        $row = $this->request->post("row/a");
        $schedule = isset($row['schedule']) ? $row['schedule'] : '';
        if (CronExpression::isValidExpression($schedule))
        {
            $this->success();
        }
        else
        {
            $this->error(__('Crontab format invalid'));
        }
    }

    /**
     * 根据Crontab表达式读取未来七次的时间
     * @internal
     */
    public function get_schedule_future()
    {
        $time = [];
        $schedule = $this->request->post('schedule');
        $days = (int) $this->request->post('days');
        try
        {
            $cron = CronExpression::factory($schedule);
            for ($i = 0; $i < $days; $i++)
            {
                $time[] = $cron->getNextRunDate(null, $i)->format('Y-m-d H:i:s');
            }
        }
        catch (\Exception $e)
        {

        }

        return json(['futuretime' => $time]);
    }
    //日志页面
    public function log_index()
    {
        if ($this->request->isAjax())
        {
            $filter = $this->request->get("filter", '');//联动搜索框
            $offset = $this->request->get("offset", 0);//起始页数
            $limit = $this->request->get("limit", 0);//结束页数
            $filter = (array)json_decode($filter, TRUE);
            $filter = $filter ? $filter : [];
            if(!empty($filter)){
                if(isset($filter['time'])){ //分割获取开始时间与结束时间
                    $arr = array_slice(explode(' - ', $filter['time']), 0, 2);
                    $start_time=(string)strtotime($arr[0]);//mongodb对类型要求一样要一致
                    $end_time=(string)strtotime($arr[1]);
                    $where['time'] = array('between', [$start_time, $end_time]);
                }
                !isset($filter['module']) ?:$where['module'] = $filter['module'];
                !isset($filter['action']) ?:$where['action'] = $filter['action'];
                !isset($filter['status']) ?:$where['code'] = (int)$filter['status'];
                $list = $this->conn ->where($where)->order('time','desc')->limit($offset,$limit)->select();
                $total = $this->conn ->where($where)->count();
            }else{
                $list = $this->conn ->order('time','desc')->limit($offset,$limit)->select();
                $total = $this->conn->count();
            }
            foreach ($list as $k=>&$v){
                $_id = (array)$v['_id'];
                $v['_id'] = $_id['oid'];
                $v['time'] = date('Y-m-d H:i:s',$v['time']);
                $v['code'] == 0 ? $v['code'] = 2 : $v['code'] = 1;
                $v['status'] = (string)$v['code'];
                unset($v['code']);
                $list[$k] = $v;
            }
            $result = array("total" => $total,'rows' => $list);
            return $result;
        }
        return $this->view->fetch();
    }
    //日志详情查看
    public function log_check($ids = NULL)
    {
        $id = new ObjectID($ids);
        $row = $this->conn ->find($id);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $_id = (array)$row['_id'];
        $row['_id'] = $_id['oid'];
        $row['server_count'] = count($row['server']);
        !isset($row['server']['argv'])?:$row['server']['argv[0]'] = $row['server']['argv'][0];
        !isset($row['server']['argv'])?:$row['server']['argv[1]'] = $row['server']['argv'][1];
        unset($row['server']['argv']);
        $row['time'] = date('Y-m-d H:i:s',$row['time']);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}