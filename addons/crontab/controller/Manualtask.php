<?php

namespace addons\crontab\controller;

use app\common\controller\Backend;
use app\common\model\Crontab;
use fast\Http;
use think\Db;
use think\Exception;
use think\Log;

/**
 * 定时任务接口
 *
 * 手动运行
 * @internal
 */
class Manualtask extends Backend
{

    protected $noNeedRight = ['*'];

    /**
     * 初始化方法,最前且始终执行
     */
    public function _initialize()
    {
        parent::_initialize();

        // 清除错误
        error_reporting(0);

        // 设置永不超时
        set_time_limit(0);
    }

    /**
     * 执行定时任务
     */
    public function index()
    {
        $id = $this->request->param("ids");
        if(!$id){
            $this->error('执行ID不能为空!');
        }
        $time = time();
        $logDir = LOG_PATH . 'crontab/';
        if (!is_dir($logDir))
        {
            mkdir($logDir, 0755);
        }
        //选择任务
        $crontab = Crontab::where('id', '=', $id)->find();
        if(!$crontab){
            $this->error('查不到执行任务!');
        }
        $update = [];
        $update['executetime'] = $time;
        $update['executes'] = $crontab['executes'] + 1;

        // 更新状态
        $crontab->save($update);

        // 将执行放在后面是为了避免超时导致多次执行
        try
        {
            if ($crontab['type'] == 'url')
            {
                if (substr($crontab['content'], 0, 1) == "/")
                {
                    // 本地项目URL
                    exec('nohup php ' . ROOT_PATH . 'public/cron.php ' . $crontab['content'] . ' >> ' . $logDir . date("Y-m-d") . '.log 2>&1 &');
                }
                else
                {
                    // 远程异步调用URL
                    Http::sendAsyncRequest($crontab['content']);
                }
            }
            else if ($crontab['type'] == 'sql')
            {
                //这里需要强制重连数据库,使用已有的连接会报2014错误
                $connect = Db::connect([], true);
                $connect->execute("select 1");
                // 执行SQL
                $connect->getPdo()->exec($crontab['content']);
            }
            else if ($crontab['type'] == 'shell')
            {
                // 执行Shell
                exec($crontab['content'] . ' >> ' . $logDir . date("Y-m-d") . '.log 2>&1 &');
            }
        }
        catch (Exception $e)
        {
            Log::record($e->getMessage());
        }
        $this->success('任务执行成功!');
    }

}
