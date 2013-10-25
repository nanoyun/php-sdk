<?php
/**
 *　获取空间使用情况
 */

//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);
$spacename = 'imagesofnanoyun';//指定查看的空间名称
$rsp = $nanoyun->getSpaceUsage($spacename);//获取空间信息
$rsp = json_decode($rsp);
var_dump($rsp);
?>