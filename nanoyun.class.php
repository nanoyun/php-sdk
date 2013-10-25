<?php
session_start();
/**
 * 客户端服务封装
 * @author nanoyun.com
 */
class Nanoyun{

    private $_server = 'api_client.nanoyun.com';
    private $_app_key;
    private $_app_secret;
    private $_token;
    private $_timeout;

    /**
     * 初始化
     * @param string $app_key
     * @param string $app_secret
     */
    public function __construct($app_key, $app_secret){
        $this->_app_key = $app_key;
        $this->_app_secret = $app_secret;
        $this->_token = $this->get_access_token();
    }

    /**
     * 获取token
     */
    public function get_access_token()
    {
        if(!isset($_SESSION['accesstoken']) || time() > $_SESSION['accesstoken']->expires_time) {
            $params = array(
                    'appkey' => $this->_app_key,
                    'appsecret' => $this->_app_secret,
                    'response_type' => 'code',
            );
            $url = 'http://'. $this->_server. '/oauth/accesstoken' .'?'. http_build_query($params);
            $accesstoken = file_get_contents($url);
            $accesstoken = json_decode($accesstoken);
            $accesstoken->expires_time = time() + $accesstoken->expires_in; //有效时间
            $_SESSION['accesstoken'] =  $accesstoken;
            $access_token = $accesstoken->access_token;
        } else {
            $access_token =  $_SESSION['accesstoken']->access_token;
        }
        return $access_token;
    }

    /**
     * 写入文件
     * @param string $fspace
     * @param string $filename
     * @param mixed $file
     * @param boolean $auto_mkdir
     * @param array $opts
     */
    public function writeFile($fspace,$filename, $file, $auto_mkdir = False, $opts = Null){
        if(is_null($opts))$opts = array();
        if ($auto_mkdir === True) $opts['Mkdir'] = 'true';
        $params = array(
                'access_token' => $this->_token,
                'filename' => $filename,
                'fspace' => $fspace,
        );
        $url = 'http://'. $this->_server. '/put.php';
        $this->_file_infos = $this->_request($url, $params ,'put', $opts, $file);
        return $this->_file_infos;
    }


    /**
     * 文件从云端读取[读取外链地址]
     *
     * @param $fspace 空间名称
     * @param $filename 文件名称
     */
    public function readFile($fspace, $filename){
        $params = array(
                'access_token' => $this->_token,
                'fname' => $filename,
                'fspace' => $fspace
        );
        $url = 'http://'. $this->_server. '/space/readFile';

        $resp = $this->_request($url, $params);
        return $resp;
    }


    /**
     * 移动文件
     * $fname 文件名
     * $new_name  新文件名
     */
    public function moveFile($fname, $new_name){
        $params = array(
                'access_token' => $this->_token,
                'fname' => $fname,
                'new_name' => $new_name
        );
        $url = 'http://'. $this->_server. '/space/moveFile';
        $resp = $this->_request($url, $params);
        return $resp;
    }

    /**
     * 复制文件
     * $name 文件名
     * $new_name  新文件名
     */
    public function copyFile($name, $new_name){
        $params = array(
                'access_token' => $this->_token,
                'fname' => $name,
                'new_name' => $new_name
        );
        $url = 'http://'. $this->_server. '/space/copyFile';
        $resp = $this->_request($url, $params);
        return $resp;
    }



    /**
     * 获取目录文件列表
     * $param $path 目录路径
     * @return json
     */
    public function getList($path = '')
    {
        $params = array('access_token' => $this->_token);
        if($path) {
            $params['path'] = $path;
        }
        $url = 'http://'. $this->_server. '/space/getList.html';
        $resp  = $this->_request($url, $params);
        return $resp;
    }

     /**
     * 删除目录或者文件
     * $param $path 目录路径或文件路径
     * @return json
     */
    public function delete($spacename, $path)
    {
        $params = array('access_token' => $this->_token, 'path' => $path, 'spacename' => $spacename);
        $url = 'http://'. $this->_server. '/space/delete.html';
        $resp  = $this->_request($url, $params, 'post');
        return $resp;
    }

     /**
     * 获取空间使用情况
     * @return json
     */
    public function getBucketUsage()
    {
        $params = array('access_token' => $this->_token);
        $url = 'http://'. $this->_server. '/user/getBucketUsage.html';

        $resp = $this->_request($url, $params);
        return $resp;
    }

     /**
     * 获取目录或文件信息
     * $path 文件 路径
     * @return json
     */
    public function getFileInfo($path)
    {
        $params = array('access_token' => $this->_token, 'path' => $path);

        $url = 'http://'. $this->_server. '/space/getFileInfo.html';
        $resp  = $this->_request($url, $params);
        return $resp;
    }

    /**
     * 创建空间(目录)
     * $param $spacename 空间名称
     * $param $dirname 创建目录名称
     * @return json
     */
    public function makeDir($spacename, $dirname)
    {
        $params = array('access_token' => $this->_token, 'spacename' => $spacename,'dirname' => $dirname);

        $url = 'http://'. $this->_server. '/space/makedir.html';
        $resp  = $this->_request($url, $params,'post');
        return $resp;
    }

   


    /**
     * 发送请求
     * @param string $uri 请求的资源
     * @param $params 接口参数   array('content'=>'test', 'format'=>'json');
     * @param string $method 请求的方法
     * @param string $headers 请求头
     * @param string $body POST请求发送数据
     * @param flow $file_handle 文件流
     */
    public function _request($uri, $params = array(), $method = 'GET', $headers = null, $body = null, $file_handle=null)
    {

        $method = strtolower($method);
        switch ($method)
        {
            case 'post':
                $ci = curl_init();
                curl_setopt($ci, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
                curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ci, CURLOPT_TIMEOUT, 3);
                curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ci, CURLOPT_HEADER, false);
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params))
                {

                    curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));

                }
                curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
                curl_setopt($ci, CURLOPT_URL, $uri);
                $response = curl_exec($ci);
                curl_close ($ci);
                break;
            default:
                if (!empty($params))
                {
                    $uri = $uri . (strpos($uri, '?') ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                $url = $uri;
                $ch = $ci = curl_init($uri);

                $_headers = array('Expect:');
                if(!is_null($headers) && is_array($headers)){
                    foreach ($headers as $key=>$value){
                        array_push($_headers, "{$key}: {$value}");
                    }
                }

                $length = 0;
                $date = date('Y-m-d H:i:s');

                if(!is_null($body)){
                    if(is_resource($body)){
                        fseek($body, 0, SEEK_END);
                        $length = ftell($body);
                        fseek($body, 0);

                        array_push($_headers, "Content-Length: {$length}");
                        curl_setopt($ch, CURLOPT_INFILE, $body);
                        curl_setopt($ch, CURLOPT_INFILESIZE, $length);
                    }else{
                        $length = @strlen($body);
                        array_push($_headers, "Content-Length: {$length}");
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                    }
                }else{
                    array_push($_headers, "Content-Length: {$length}");
                }

                array_push($_headers, "Date: {$date}");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);

                curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
                //curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

                if(strtolower($method) == 'put'){
                    curl_setopt($ch, CURLOPT_POST, 1);
                }else{
                    curl_setopt($ch, CURLOPT_POST, 0);
                }
                if(strtolower($method) == 'get' && is_resource($file_handle)){
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FILE, $file_handle);
                }

                if ($method == 'HEAD') {
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                }

                $response = curl_exec($ch);
                $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            break;

        }
        return $response;
    }
    /**
     * 处理HTTP HEADERS中返回的自定义数据
     *
     * @param string $text header字符串
     *
     * @return array
     */
    private function _getHeadersData($text) {/*{{{*/
        $headers = explode("\r\n", $text);
        $items = array();
        foreach($headers as $header) {
            $header = trim($header);
            if(strpos($header, 'x-upyun') !== False){
                list($k, $v) = explode(':', $header);
                $items[trim($k)] = in_array(substr($k,8,5), array('width','heigh','frame')) ? intval($v) : trim($v);
            }
        }
        return $items;
    }/*}}}*/
}

