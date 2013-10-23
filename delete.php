<?php
/**
 *　删除文件或目录
 * $path 目录路径或文件 路径
 */

include_once "config.php";
include_once "nanoyun.class.php";

$nanoyun = new Nanoyun($app_key, $app_secret);
//$path = 'op4'; //目录
$path = '1.jpg';
$info = $nanoyun->delete($path);
$info = json_decode($info);
print_r($info);
?>
