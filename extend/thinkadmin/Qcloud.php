<?php
/**
 * 腾讯云上传SDK
 *
 * Created by PhpStorm.
 * User: onming(17089325@qq.com)
 * Date: 2018/09/10
 */
namespace thinkadmin;

use Qcloud\Cos\Client;
use Guzzle\Service\Resource\Model;

class Qcloud
{
    protected $cosClient;

    public function __construct()
    {
        $this->cosClient = new Client(array(
            'region' => 'ap-shanghai', #地域，如ap-guangzhou,ap-beijing-1
            'credentials' => array(
                'secretId' => 'AKIDc6gHrNBxynPp9tridMtFPRK4g7Pd4iW8',
                'secretKey' => 'LSTMOk5GF3gDds46c2FMI5080nPp5A92',
            ),
        ));
    }

    /**
     * 上传文件
     *
     * @param $bucket
     * @param $key
     * @param $body
     * @return mixed
     */
    public function putObject($bucket, $key, $body)
    {
        try {
            return collection($this->cosClient->putObject(array(
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $body)))->toArray();
        } catch (\Exception $e) {
            return "$e\n";
        }
    }

    /**
     * 文件列表
     *
     * @param $bucket
     * @return mixed
     */
    public function listObjects($bucket)
    {
        return $this->cosClient->listObjects(array('Bucket' => $bucket));
    }



}