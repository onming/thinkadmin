<?php
/**
 * 加密类
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/09/10
 */
namespace thinkadmin;

class Encrypt
{

    /**
     * 生成sign
     *
     * @param $data
     * @param $token
     * @return string
     */
    public static function getSign($data, $token)
    {
        // data和sign排除
        if(isset($data['data']))
            unset($data['data']);
        if(isset($data['sign']))
            unset($data['sign']);

        sort($data, SORT_STRING);
        $str = join($data) . $token;
        return md5($str);
    }

    /**
     * 检验sign
     *
     * @param $data
     * @param $token
     * @return bool
     */
    public static function checkSign($data, $token)
    {
        if (0 == strcmp($data['sign'], self::getSign($data, $token))) {
            return $data;
        }
        return false;
    }



}