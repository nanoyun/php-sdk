<?php
/**
 *　创建目录
 */

//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);
$spacename = 'imagesofnanoyun';
$dirname = "data2/cate1";
$rsp = $nanoyun->makeDir($spacename, $dirname);
$rsp = json_decode($rsp);
var_dump($rsp);
?>