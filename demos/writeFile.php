<?php
/**
 * 写入文件测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);
$filehandle = fopen('../data/foru.jpg', 'rb');
$filename = '/data/foru.jpg';//指定在云存储中的写入位置
$rsp = $nanoyun->write_file(SPACENAME, $filename, $filehandle);
var_dump(json_decode($rsp));
fclose($filehandle);