<?php

namespace addons\cms\controller;

use think\Config;
use think\Db;

/**
 * CMS首页控制器
 * Class Index
 * @package addons\cms\controller
 */
class Index extends Base
{

    public function index()
    {
        Config::set('cms.title', __('Home'));
        Config::set('cms.keywords', '');
        Config::set('cms.description', '');
        return $this->view->fetch('/index');
    }

}
