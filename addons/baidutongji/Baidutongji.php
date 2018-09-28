<?php

namespace addons\baidutongji;

use app\common\library\Menu;
use think\Addons;
use think\Session;

/**
 * 百度统计插件
 */
class Baidutongji extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'baidutongji',
                'title'   => '百度统计',
                'icon'    => 'fa fa-bar-chart',
                'sublist' => [
                    [
                        'name'  => 'baidutongji/index',
                        'title' => '网站列表',
                    ],
                    [
                        'name'  => 'baidutongji/detail',
                        'title' => '网站详情',
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('baidutongji');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('baidutongji');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('baidutongji');
        return true;
    }

    public function adminLogoutAfter(& $params)
    {
        Session::clear("baidutongji");
    }

}
