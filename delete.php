<?php
/**
 *　删除文件或目录
 */
//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);
$spacename = 'images00'; //nanoyun中创建的空间名

//删除目录测试
//$dir = 'op4'; //指定删除的目录
// $info = $nanoyun->delete($dir);
//删除文件测试
$path = '/data';//指定删除的文件
$info = $nanoyun->delete($spacename, $path);//执行删除莋
$info = json_decode($info);
var_dump($info);//输出返回信息
?>