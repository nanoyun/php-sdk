<?php
/**
 * 删除文件或目录测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);

//删除文件
$filename = '/data/foru.jpg';//云存储中的文件位置
$rsp = $nanoyun->delete(SPACENAME, $filename);

//删除目录
// $dirname = '/data2';
// $rsp = $nanoyun->delete(SPACENAME, $dirname);

var_dump(json_decode($rsp));