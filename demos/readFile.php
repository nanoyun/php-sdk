<?php
/**
 * 读取文件测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);
$filename = '/data/foru.jpg';//云存储中的文件位置
$rsp = $nanoyun->read_file(SPACENAME, $filename);
var_dump(json_decode($rsp));