<?php

namespace addons\cos\library;

class Auth
{

    const STSURL = 'https://sts.api.qcloud.com/v2/index.php';
    const DOMAIN = 'sts.api.qcloud.com';

    /**
     * 获取签名
     * @param $method
     * @param $pathname
     * @return array
     */
    public static function getAuthorization($method = 'POST', $pathname = '/')
    {
        $cosConfig = get_addon_config('cos');
        $keys = self::getTempKeys();
        // 获取个人 API 密钥 https://console.qcloud.com/capi
        $SecretId = $keys['credentials']['tmpSecretId'];
        $SecretKey = $keys['credentials']['tmpSecretKey'];
        $SessionToken = $keys['credentials']['sessionToken'];

        // 整理参数
        $query = array();
        $headers = array();
        $method = strtolower($method ? $method : 'get');
        $pathname = $pathname ? $pathname : '/';
        substr($pathname, 0, 1) != '/' && ($pathname = '/' . $pathname);

        // 签名有效起止时间
        $now = time() - 1;
        $expired = $now + $cosConfig['expire']; // 签名过期时刻，600 秒后

        // 要用到的 Authorization 参数列表
        $qSignAlgorithm = 'sha1';
        $qAk = $SecretId;
        $qSignTime = $now . ';' . $expired;
        $qKeyTime = $now . ';' . $expired;
        $qHeaderList = strtolower(implode(';', self::getObjectKeys($headers)));
        $qUrlParamList = strtolower(implode(';', self::getObjectKeys($query)));

        // 签名算法说明文档：https://www.qcloud.com/document/product/436/7778
        // 步骤一：计算 SignKey
        $signKey = hash_hmac("sha1", $qKeyTime, $SecretKey);

        // 步骤二：构成 FormatString
        $formatString = implode("\n", array(strtolower($method), $pathname, self::obj2str($query), self::obj2str($headers), ''));

        //header('x-test-method', $method);
        //header('x-test-pathname', $pathname);

        // 步骤三：计算 StringToSign
        $stringToSign = implode("\n", array('sha1', $qSignTime, sha1($formatString), ''));

        // 步骤四：计算 Signature
        $qSignature = hash_hmac('sha1', $stringToSign, $signKey);

        // 步骤五：构造 Authorization
        $authorization = implode('&', array(
            'q-sign-algorithm=' . $qSignAlgorithm,
            'q-ak=' . $qAk,
            'q-sign-time=' . $qSignTime,
            'q-key-time=' . $qKeyTime,
            'q-header-list=' . $qHeaderList,
            'q-url-param-list=' . $qUrlParamList,
            'q-signature=' . $qSignature
        ));

        return [$authorization, $SessionToken];
    }

    // 工具方法
    private static function getObjectKeys($obj)
    {
        $list = array_keys($obj);
        sort($list);
        return $list;
    }

    private static function obj2str($obj)
    {
        $list = array();
        $keyList = self::getObjectKeys($obj);
        $len = count($keyList);
        for ($i = 0; $i < $len; $i++) {
            $key = $keyList[$i];
            $val = isset($obj[$key]) ? $obj[$key] : '';
            $key = strtolower($key);
            $list[] = rawurlencode($key) . '=' . rawurlencode($val);
        }
        return implode('&', $list);
    }


    // obj 转 query string
    private static function json2str($obj)
    {
        ksort($obj);
        $arr = array();
        foreach ($obj as $key => $val) {
            array_push($arr, $key . '=' . $val);
        }
        return join('&', $arr);
    }

    // 计算临时密钥用的签名
    private static function getSignature($opt, $key, $method)
    {
        $formatString = $method . self::DOMAIN . '/v2/index.php?' . self::json2str($opt);
        $sign = hash_hmac('sha1', $formatString, $key);
        $sign = base64_encode(hex2bin($sign));
        return $sign;
    }

    // 获取临时密钥
    private static function getTempKeys()
    {
        $cosConfig = get_addon_config('cos');
        $config = array(
            'Url'         => self::STSURL,
            'Domain'      => self::DOMAIN,
            'Proxy'       => '',
            'SecretId'    => $cosConfig['secretid'], // 固定密钥
            'SecretKey'   => $cosConfig['secretkey'], // 固定密钥
            'Bucket'      => $cosConfig['bucket'],
            'Region'      => $cosConfig['region'],
            'AllowPrefix' => '*', // 这里改成允许的路径前缀，这里可以根据自己网站的用户登录态判断允许上传的目录，例子：* 或者 a/* 或者 a.jpg
        );

        $ShortBucketName = substr($config['Bucket'], 0, strripos($config['Bucket'], '-'));
        $AppId = substr($config['Bucket'], 1 + strripos($config['Bucket'], '-'));
        $policy = array(
            'version'   => '2.0',
            'statement' => array(
                array(
                    'action'    => array(
                        // // 这里可以从临时密钥的权限上控制前端允许的操作
                        //  'name/cos:*', // 这样写可以包含下面所有权限

                        // // 列出所有允许的操作
                        // // ACL 读写
                        // 'name/cos:GetBucketACL',
                        // 'name/cos:PutBucketACL',
                        // 'name/cos:GetObjectACL',
                        // 'name/cos:PutObjectACL',
                        // // 简单 Bucket 操作
                        // 'name/cos:PutBucket',
                        // 'name/cos:HeadBucket',
                        // 'name/cos:GetBucket',
                        // 'name/cos:DeleteBucket',
                        // 'name/cos:GetBucketLocation',
                        // // Versioning
                        // 'name/cos:PutBucketVersioning',
                        // 'name/cos:GetBucketVersioning',
                        // // CORS
                        // 'name/cos:PutBucketCORS',
                        // 'name/cos:GetBucketCORS',
                        // 'name/cos:DeleteBucketCORS',
                        // // Lifecycle
                        // 'name/cos:PutBucketLifecycle',
                        // 'name/cos:GetBucketLifecycle',
                        // 'name/cos:DeleteBucketLifecycle',
                        // // Replication
                        // 'name/cos:PutBucketReplication',
                        // 'name/cos:GetBucketReplication',
                        // 'name/cos:DeleteBucketReplication',
                        // // 删除文件
                        // 'name/cos:DeleteMultipleObject',
                        // 'name/cos:DeleteObject',
                        // 简单文件操作
                        'name/cos:PutObject',
                        'name/cos:PostObject',
                        'name/cos:AppendObject',
                        'name/cos:GetObject',
                        'name/cos:HeadObject',
                        'name/cos:OptionsObject',
                        'name/cos:PutObjectCopy',
                        'name/cos:PostObjectRestore',
                        // 分片上传操作
                        'name/cos:InitiateMultipartUpload',
                        'name/cos:ListMultipartUploads',
                        'name/cos:ListParts',
                        'name/cos:UploadPart',
                        'name/cos:CompleteMultipartUpload',
                        'name/cos:AbortMultipartUpload',
                    ),
                    'effect'    => 'allow',
                    'principal' => array('qcs' => array('*')),
                    'resource'  => array(
                        'qcs::cos:' . $config['Region'] . ':uid/' . $AppId . ':prefix//' . $AppId . '/' . $ShortBucketName . '/',
                        'qcs::cos:' . $config['Region'] . ':uid/' . $AppId . ':prefix//' . $AppId . '/' . $ShortBucketName . '/' . $config['AllowPrefix']
                    )
                )
            )
        );

        $policyStr = str_replace('\\/', '/', json_encode($policy));

        // 有效时间小于 30 秒就重新获取临时密钥，否则使用缓存的临时密钥
        if (isset($_SESSION['tempKeysCache']) && isset($_SESSION['tempKeysCache']['expiredTime']) && isset($_SESSION['tempKeysCache']['policyStr']) &&
            $_SESSION['tempKeysCache']['expiredTime'] - time() > 30 && $_SESSION['tempKeysCache']['policyStr'] === $policyStr) {
            return $_SESSION['tempKeysCache'];
        }

        $Action = 'GetFederationToken';
        $Nonce = rand(10000, 20000);
        $Timestamp = time() - 1;
        $Method = 'GET';

        $params = array(
            'Action'          => $Action,
            'Nonce'           => $Nonce,
            'Region'          => '',
            'SecretId'        => $config['SecretId'],
            'Timestamp'       => $Timestamp,
            'durationSeconds' => 7200,
            'name'            => '',
            'policy'          => $policyStr
        );
        $params['Signature'] = urlencode(self::getSignature($params, $config['SecretKey'], $Method));
        $url = $config['Url'] . '?' . self::json2str($params);
        $ch = curl_init($url);
        $config['Proxy'] && curl_setopt($ch, CURLOPT_PROXY, $config['Proxy']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (curl_errno($ch)) $result = curl_error($ch);
        curl_close($ch);

        $result = json_decode($result, 1);
        if (isset($result['data'])) $result = $result['data'];


        $_SESSION['tempKeysCache'] = $result;
        $_SESSION['tempKeysCache']['policyStr'] = $policyStr;

        return $result;
    }
}