<?php
/**
 * 读取文件测试
 */
//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);
$spacename = 'imagesofnanoyun';//nanoyun中创建的空间名
$filename = '/data/foru.jpg';//读取文件基于nanoyun空间的存储位置
$rsp = $nanoyun->readFile($spacename, $filename);
$rsp = json_decode($rsp);
var_dump($rsp);//输出返回json串
?>