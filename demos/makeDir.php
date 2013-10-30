<?php
/**
 * 创建目录测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);
$dirname = '/data2/a/b/c';//云存储中的文件位置
$rsp = $nanoyun->make_dir(SPACENAME, $dirname);
var_dump(json_decode($rsp));