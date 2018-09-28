<?php

namespace app\admin\controller;


use addons\baidutongji\library\LoginService;
use addons\baidutongji\library\ReportService;
use app\common\controller\Backend;
use think\Exception;
use think\Session;

/**
 * 百度统计
 *
 * @icon fa fa-bar-chart
 */
class Baidutongji extends Backend
{
    // preLogin,doLogin URL
    protected $loginUrl = 'https://api.baidu.com/sem/common/HolmesLoginService';

    // Tongji API URL
    protected $apiUrl = 'https://api.baidu.com/json/tongji/v1/ReportService';

    // Baidu username
    protected $username;

    // Baidu password
    protected $password;

    // Token
    protected $token;

    // [memo] ZhanZhang:1,FengChao:2,Union:3,Columbus:4
    protected $accountType = 1;

    // [memo] UUID, used to identify your device, for instance: MAC address
    protected $uuid = 'Fastadmin';

    protected $reportService;

    protected $noNeedRight = ['clearcache'];

    protected $indicators = [
        'pv_count'      => '浏览量(PV)',
        'visitor_count' => '访客数(UV)',
        'ip_count'      => 'IP 数'
    ];

    public function _initialize()
    {
        parent::_initialize();
        $config = get_addon_config('baidutongji');
        if (empty($config['usernmae']) || empty($config['password']) || empty($config['token'])) {
            $this->error("请先在后台配置该插件。", 'addon/index');
        }
        $this->username = $config['usernmae'];
        $this->password = $config['password'];
        $this->token = $config['token'];

        $tongji = Session::get("baidutongji");
        if (!$tongji) {
            $loginService = new LoginService($this->loginUrl, $this->uuid);
            // preLogin
            if (!$loginService->preLogin($this->username, $this->token)) {
                $this->error("请检查插件配置中用户名、Token是否正确");
            }
            // doLogin
            $ret = $loginService->doLogin($this->username, $this->password, $this->token);
            if ($ret) {
                $tongji = $ret;
                Session::set("baidutongji", $ret);
            } else {
                $this->error("请检查插件配置中用户名、密码、Token是否正确");
            }

        }
        $this->reportService = new ReportService($this->apiUrl, $this->username, $this->token, $tongji['ucid'], $this->password);
    }

    /**
     * 站点列表页
     */
    public function index()
    {

        try {
            if ($this->request->isAjax()) {
                // get site list
                $ret = $this->reportService->getSiteList();
                if ($ret['header']['status'] == 0) {
                    $list = $ret['body']['data'][0]['list'];
                    // array_walk($list,function(&$value,$key){
                    //     $value['status'] = ($value['status'] == 0) ? 'normal' : 'hidden';
                    // });                    
                    foreach ($list as $k => $v) {
                        $list[$k]['status'] = ($v['status'] == 0) ? 'normal' : 'hidden';
                        $list[$k]['id'] = $v['site_id'];
                    }
                }
                return json(['rows' => $list, 'total' => count($list)]);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage(), null, ['token' => $this->request->token()]);
        }
        return $this->view->fetch();
    }


    /**
     * 站点详情页
     */
    public function detail()
    {
        try {
            $siteid = $this->request->param('ids', null);
            if ($this->request->isAjax()) {
                $step = $this->request->param('step', null);
                $start = $this->request->param('stime', time());
                $gran = $this->request->param('gran', 'hour');
                $start = date('Ymd', $start);
                $end = date('Ymd');

                if (!isset($siteid) || !isset($step)) {
                    throw new Exception("请求参数错误，请不要恶意破坏");
                }
                switch ($step) {
                    case 'trend':
                        $result = $this->getTrend($siteid, $start, $end, $gran);
                        break;
                    case 'enter':
                        $result = $this->getEnter($siteid, $start, $end);
                        break;
                    case 'access':
                        $result = $this->getAccess($siteid, $start, $end);
                        break;
                    case 'chinamap':
                        $result = $this->getChinaMap($siteid, $start, $end);
                        break;
                    case 'inbie':
                        $result = $this->getBie($siteid, $start, $end);
                        break;
                    default:
                        # code...
                        break;
                }
                return json($result);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage(), null, ['token' => $this->request->token()]);
        }
        $this->assignconfig('api', url('baidutongji/detail'));
        $this->assignconfig('siteid', $siteid);
        return $this->view->fetch();
    }

    /**
     * 清除缓存
     * @internal
     */
    public function clearcache()
    {
        Session::clear('baidutongji');
        $this->success("清除成功！");
    }

    /**
     * 饼图
     */
    private function getBie($sid, $start, $end)
    {
        $list = [];
        $result = $this->reportService->getData([
            'site_id'    => $sid,
            'method'     => 'source/all/a',
            'start_date' => $start,
            'end_date'   => $end,
            'metrics'    => 'pv_count',
            'viewType'   => 'type'
        ]);

        if ($result['header']['status'] === 0) {
            // 维度数据
            $xdata = array_values($result['body']['data'][0]['result']['items'][0]);
            // 指标数据
            $indidata = array_values($result['body']['data'][0]['result']['items'][1]);
            $legend = [];
            $series = [];
            if (is_array($xdata) && !empty($xdata)) {
                foreach ($xdata as $key => $value) {
                    $legend[] = $value[0]['name'];
                    $series[] = [
                        'value' => $indidata[$key][0],
                        'name'  => $value[0]['name']
                    ];
                }
            }
        }
        return ['legend' => $legend, 'series' => $series];
    }

    /**
     * 地域分布图
     */
    private function getChinaMap($sid, $start, $end)
    {
        $result = $this->reportService->getData([
            'site_id'    => $sid,
            'method'     => 'visit/district/a',
            'start_date' => $start,
            'end_date'   => $end,
            'metrics'    => 'pv_count,pv_ratio'
        ]);
        $list = [];
        if ($result['header']['status'] === 0) {
            $max = 0;
            // 维度数据
            $xdata = array_values($result['body']['data'][0]['result']['items'][0]);
            // 指标数据
            $indidata = array_values($result['body']['data'][0]['result']['items'][1]);
            if (is_array($xdata)) {
                foreach ($xdata as $key => $value) {
                    if ($indidata[$key][0] > $max) $max = $indidata[$key][0];
                    $list[$key]['name'] = $value[0]['name'];
                    $list[$key]['value'] = $indidata[$key][0];
                    //$indidata[$key][1] 占比
                }
            }
        }
        return ['max' => $max, 'citymap' => $list];
    }

    /**
     * Top10受访问页面
     */
    private function getAccess($sid, $start, $end)
    {
        $list = [];
        $result = $this->reportService->getData([
            'site_id'    => $sid,
            'method'     => 'visit/toppage/a',
            'start_date' => $start,
            'end_date'   => $end,
            'metrics'    => 'pv_count'
        ]);
        if ($result['header']['status'] === 0) {
            // 维度数据
            $xdata = array_values($result['body']['data'][0]['result']['items'][0]);
            $sum = $result['body']['data'][0]['result']['sum'][0][0];
            // 指标数据
            $indidata = array_values($result['body']['data'][0]['result']['items'][1]);
            foreach ($indidata as $key => $value) {
                $temp = [
                    'name'  => $xdata[$key][0]['name'],
                    'count' => $value[0],
                    'perc'  => sprintf("%.2f", ($value[0] / $sum) * 100)
                ];
                $list[$key] = $temp;
            }
        }
        return $list;
    }

    /**
     * Top10入口页面
     */
    private function getEnter($sid, $start, $end)
    {
        $list = [];
        $result = $this->reportService->getData([
            'site_id'    => $sid,
            'method'     => 'visit/landingpage/a',
            'start_date' => $start,
            'end_date'   => $end,
            'metrics'    => 'pv_count'
        ]);
        if ($result['header']['status'] === 0) {
            // 维度数据
            $xdata = array_values($result['body']['data'][0]['result']['items'][0]);
            $sum = $result['body']['data'][0]['result']['sum'][0][0];
            // 指标数据
            $indidata = array_values($result['body']['data'][0]['result']['items'][1]);
            foreach ($indidata as $key => $value) {
                $temp = [
                    'name'  => $xdata[$key][0]['name'],
                    'count' => $value[0],
                    'perc'  => sprintf("%.2f", ($value[0] / $sum) * 100)
                ];
                $list[$key] = $temp;
            }
        }
        return $list;
    }

    /**
     * 趋势图
     *
     * @param integer $sid 网站ID
     * @param string $start 开始时间
     * @param string $end 截止时间
     * @param string $gran 粒度（day/hour/week/month）
     * @return array
     */
    private function getTrend($sid, $start, $end, $gran = 'hour')
    {
        $result = $this->reportService->getData([
            'site_id'    => $sid,
            'method'     => 'trend/time/a',
            'start_date' => $start,
            'end_date'   => $end,
            'gran'       => $gran,
            'metrics'    => 'pv_count,visitor_count'
        ]);

        if ($result['header']['status'] === 0) {
            // 时间区间
            $xaxis = array_reverse(array_values($result['body']['data'][0]['result']['items'][0]));
            // 整点
            // array_walk($xaxis,function(&$value,$key){
            //     $value = explode('-',$value[0])[0];
            // });
            $tempdata = array_values($result['body']['data'][0]['result']['items'][1]);
            $seriesdata[0] = array_column($tempdata, 0);
            $seriesdata[1] = array_column($tempdata, 1);
            // $seriesdata[2] = array_column($tempdata,2);
            $fields = $result['body']['data'][0]['result']['fields'];
            foreach ($fields as $key => $value) {
                if ($key == 0) continue;
                $series[] = ['name' => $this->indicators[$value], 'type' => 'line', 'data' => array_reverse($seriesdata[$key - 1])];
            }
        }
        return ['legend' => [$this->indicators['pv_count'], $this->indicators['visitor_count']], 'xaxis' => $xaxis, 'series' => $series];
    }
}
