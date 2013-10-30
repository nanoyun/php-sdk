<?php
/**
 * 读取当前空间信息测试
 */
include "../config.php";//导入appkey、appsecret、空间名称
include "../nanoyun.class.php";

$nanoyun = new Nanoyun(APPKEY, APPSECRET);

$rsp = $nanoyun->get_space_usage(SPACENAME);
var_dump(json_decode($rsp));