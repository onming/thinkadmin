<?php

namespace addons\cos\controller;

use addons\cos\library\Auth;
use app\common\model\Attachment;
use think\addons\Controller;

/**
 * COS
 *
 */
class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    public function params()
    {
        $config = get_addon_config('cos');
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $path = $this->request->post('path');
        if("" != trim($path)){
            $filename = ltrim($path, "/");
        }else{
            $suffix = substr($name, stripos($name, '.') + 1);
            $search = ['{year}', '{mon}', '{month}', '{day}', '{filemd5}', '{suffix}', '{.suffix}', '{filename}'];
            $replace = [date("Y"), date("m"), date("m"), date("d"), $md5, $suffix, '.' . $suffix, $name];
            $filename = ltrim(str_replace($search, $replace, $config['savekey']), '/');
        }

        list($signature, $token) = Auth::getAuthorization();
        $params = [
            'key'             => $filename,
            'filename'        => basename($filename),
            'signature'       => $signature,
            'token'           => $token,
            'notifysignature' => md5($signature)
        ];
        $this->success('', null, $params);
        return;
    }

    public function notify()
    {
        $size = $this->request->post('size');
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $type = $this->request->post('type');
        $signature = $this->request->post('signature');
        $notifysignature = $this->request->post('notifysignature');
        $url = $this->request->post('url');
        $suffix = substr($name, stripos($name, '.') + 1);
        if ($notifysignature == md5($signature)) {
            $attachment = Attachment::getBySha1($md5);
            if (!$attachment) {
                $params = array(
                    'admin_id'    => (int)session('admin.id'),
                    'user_id'     => (int)cookie('uid'),
                    'filesize'    => $size,
                    'imagewidth'  => 0,
                    'imageheight' => 0,
                    'imagetype'   => $suffix,
                    'imageframes' => 0,
                    'mimetype'    => $type,
                    'url'         => $url,
                    'uploadtime'  => time(),
                    'storage'     => 'cos',
                    'sha1'        => $md5,
                );
                Attachment::create($params);
            }
            $this->success();
        } else {
            $this->error(__('You have no permission'));
        }
        return;
    }

}
