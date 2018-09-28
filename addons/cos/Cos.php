<?php

namespace addons\cos;

use app\common\library\Menu;
use Qcloud\Cos\Client;
use think\Addons;
use think\Loader;

/**
 * COS插件
 */
class Cos extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {

        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {

        return true;
    }

    /**
     * 渲染命名空间配置信息
     */
    public function appInit()
    {
        //添加支付包的命名空间
        Loader::addNamespace('Qcloud', ADDON_PATH . 'cos' . DS . 'library' . DS . 'Qcloud' . DS);
    }

    /**
     *
     */
    public function uploadConfigInit(&$upload)
    {
        $cosConfig = $this->getConfig();
        $upload = [
            'cdnurl'    => $cosConfig['cdnurl'],
            'uploadurl' => $cosConfig['uploadurl'],
            'bucket'    => $cosConfig['bucket'],
            'maxsize'   => $cosConfig['maxsize'],
            'mimetype'  => $cosConfig['mimetype'],
            'multipart' => [],
            'multiple'  => $cosConfig['multiple'] ? true : false,
        ];
    }

}
