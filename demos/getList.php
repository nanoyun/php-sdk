<?php
/**
 *　获取目录中的列表
 */

//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);

$spacename = 'imagesofnanoyun';//空间名称
$dirname = '/data';//查询目录
$rsp = $nanoyun->getList($spacename, $dirname);
var_dump($rsp);
?>