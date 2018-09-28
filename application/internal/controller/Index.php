<?php
/**
 * internal内部接口
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2016/6/30
 */
namespace app\internal\controller;

use app\common\controller\Internal;

class Index extends Internal
{

    public function __construct()
    {
        $input = file_get_contents('php://input');
        $input = json_decode(urldecode($input), true);
        // 兼容旧版
        if(!empty($input['module'])){
            $controller = controller($input['module']);
            // 本地接口访问不到去旧接口请求
            if (!method_exists($controller, $input['action'])) {
                if(is_online_env()){
                    $url = 'http://xxx/api.html';
                }else{
                    $url = 'http://www.work.xxx.cn/api.html';
                }
                $rs = curl_post_data($url, file_get_contents('php://input'));
                // 记录日志
                $rs['debug'] = 'curl:'.$url;
                $this->saveLog($rs);
                echo json_encode($rs);
                exit();
            }
            $controller->actionInit($input);
        }
    }

    /**
     * 默认入口
     */
    public function index()
    {

    }

}
