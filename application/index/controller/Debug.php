<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Debug extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {

    }

    public function test()
    {

    }

}
