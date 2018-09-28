<?php
use think\Env;
return array (
  'app_key' => 
  array (
    'name' => 'appid',
    'title' => 'AppID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请前往腾讯控制台 > 访问管理 > API密钥',
    'ok' => '',
    'extend' => '',
  ),
  'secretid' => 
  array (
    'name' => 'secretid',
    'title' => 'SecretId',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请前往腾讯控制台 > 访问管理 > API密钥',
    'ok' => '',
    'extend' => '',
  ),
  'secretkey' => 
  array (
    'name' => 'secretkey',
    'title' => 'SecretKey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请前往腾讯控制台 > 访问管理 > API密钥',
    'ok' => '',
    'extend' => '',
  ),
  'bucket' => 
  array (
    'name' => 'bucket',
    'title' => '存储桶名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '存储空间名称',
    'ok' => '',
    'extend' => '',
  ),
  'region' => 
  array (
    'name' => 'region',
    'title' => '地域名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'ap-shanghai',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请输入地域简称,请注意使用英文',
    'ok' => '',
    'extend' => '',
  ),
  'uploadurl' => 
  array (
    'name' => 'uploadurl',
    'title' => '上传接口地址',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请输入你的上传接口地址',
    'ok' => '',
    'extend' => '',
  ),
  'cdnurl' => 
  array (
    'name' => 'cdnurl',
    'title' => 'CDN地址',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请配置你的CDN地址或在存储桶基础配置中获取',
    'ok' => '',
    'extend' => '',
  ),
  'savekey' => 
  array (
    'name' => 'savekey',
    'title' => '保存文件名',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'expire' => 
  array (
    'name' => 'expire',
    'title' => '上传有效时长',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1800',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'maxsize' => 
  array (
    'name' => 'maxsize',
    'title' => '最大可上传',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '300M',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'mimetype' => 
  array (
    'name' => 'mimetype',
    'title' => '可上传后缀格式',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx,apk,ipa,plist',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'multiple' => 
  array (
    'name' => 'multiple',
    'title' => '多文件上传',
    'type' => 'bool',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
