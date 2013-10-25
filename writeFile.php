<?php
/**
 * 写入文件测试
 */
//导入配置文件，包含appkey和appsecret设置
include_once "config.php";
//导入核心封装库
include_once "nanoyun.class.php";
//创建nanoyun对象实例
$nanoyun = new Nanoyun($app_key, $app_secret);
$filehandle = fopen('data/foru.jpg', 'rb');//打开要上传文件的文件流
$spacename = 'imagesofnanoyun'; //nanoyun中创建的空间名
$filename = '/data/foru.jpg';//Nanoyun空间的存储位置
$rsp = $nanoyun->writeFile($spacename, $filename, $filehandle);//写入文件流到nanoyun端
fclose($filehandle);//关闭文件流
$rsp = json_decode($rsp);
var_dump($rsp);//输出返回json串
// echo $rsp->success;
// echo '<br/>';
// echo $rsp->error;
?>