<?php
/**
 * 列出目录下所有的文件测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);
$dirname = '/data';//指定目录
$rsp = $nanoyun->get_list(SPACENAME, $dirname);
var_dump(json_decode($rsp));