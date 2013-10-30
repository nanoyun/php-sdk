<?php
//开启回话，保存token
session_start();

/**
 * Nano云存储服务
 */
class Nanoyun{

    //服务所在域名
    private $_server = 'api.nanoyun.com';
    //app key
    private $_app_key;
    //app secret
    private $_app_secret;
    //app token
    private $_token;
    //app timeout
    private $_timeout = 300;

    /**
     * 初始化参数
     * @param string $app_key
     * @param string $app_secret
     */
    public function __construct($app_key, $app_secret){
        $this->_app_key = $app_key;
        $this->_app_secret = $app_secret;
        $this->_token = $this->__get_token();
    }

    /**
     * 权限校验
     */
    private function __get_token(){
        if(!isset($_SESSION['_access_token']) || time() > $_SESSION['_access_token']->expires_time){
            $params = array(
                'appkey' => $this->_app_key,
                'appsecret' => $this->_app_secret,
                'response_type' => 'code',
            );
            $rsp = json_decode(file_get_contents($this->_change_uri_to_url('/oauth/accesstoken?'. http_build_query($params))));
            $rsp->expires_time = time() + $rsp->expires_in;
            $_SESSION['_access_token'] = $rsp;
            return $rsp->access_token;
        }else{
            return $_SESSION['_access_token']->access_token;
        }
    }

    /**
     * 执行请求
     * @param string $url
     * @param array $app_secret
     * @param string $method
     * @param mixed $filehandle
     */
    private function _do_request($url, $params = array(), $method = 'post', $filehandle = null){
        $method = strtolower($method);
        if(!empty($params)){
            $params['access_token'] = $this->_token;
        }
        $req_conn = curl_init();
        switch($method){
            case 'post':
                curl_setopt($req_conn, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
                curl_setopt($req_conn, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($req_conn, CURLOPT_TIMEOUT, 3);
                curl_setopt($req_conn, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($req_conn, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($req_conn, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($req_conn, CURLOPT_HEADER, false);
                curl_setopt($req_conn, CURLOPT_POST, true);
                if(!empty($params)){
                    curl_setopt($req_conn, CURLOPT_POSTFIELDS, http_build_query($params));
                }
                curl_setopt($req_conn, CURLINFO_HEADER_OUT, true);
                curl_setopt($req_conn, CURLOPT_URL, $url);
                break;
            case 'put':
                // echo "$url,method=$method,filehandle=$filehandle\n";
                if(!empty($params)){
                    $url = $url. (strpos($url, '?')?'&':'?'). http_build_query($params);
                }
                curl_setopt($req_conn, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
                curl_setopt($req_conn, CURLOPT_URL, $url);
                $_headers = array('Expect:');
                if(!is_null($filehandle)){
                    if(is_resource($filehandle)){
                        // echo '是文件';
                        fseek($filehandle, 0, SEEK_END);
                        $length = ftell($filehandle);
                        fseek($filehandle, 0);
                        array_push($_headers, "Content-Length: {$length}");
                        curl_setopt($req_conn, CURLOPT_INFILE, $filehandle);
                        curl_setopt($req_conn, CURLOPT_INFILESIZE, $length);
                    }else{
                        curl_close($req_conn);
                        throw new Exception("filehandle must be resource type");
                    }
                }
                $date = date('Y-m-d H:i:s', time());
                array_push($_headers, "Date: {$date}");
                curl_setopt($req_conn, CURLOPT_HTTPHEADER, $_headers);
                curl_setopt($req_conn, CURLOPT_TIMEOUT, $this->_timeout);
                curl_setopt($req_conn, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($req_conn, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($req_conn, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($req_conn, CURLOPT_POST, 1);
                break;
            default:
                curl_close($req_conn);
                throw new Exception("unsupported method called.");
                break;
        }
        $response = curl_exec($req_conn);
        curl_close($req_conn);
        return $response;
    }

    /**
     * 将uri转为url
     * @param string $uri
     */
    private function _change_uri_to_url($uri){
        return 'http://'. $this->_server. $uri;
    }

    /**
     * 写入文件流
     * @param string $spacename
     * @param string $filename
     * @param mixed $filehandle
     */
    public function write_file($spacename, $filename, $filehandle){
        $params = array(
            'spacename' => $spacename,
            'filename' => $filename,
        );
        return $this->_do_request($this->_change_uri_to_url('/put.php'), $params, 'put', $filehandle);
    }

    /**
     * 读取文件信息
     * @param string $spacename
     * @param string $filename
     * @param mixed $filehandle
     */
    public function read_file($spacename, $filename){
        $params = array(
            'spacename' => $spacename,
            'filename' => $filename,
        );
        return $this->_do_request($this->_change_uri_to_url('/space/readFile'), $params);
    }

    /**
     * 删除目录或文件
     * @param string $spacename
     * @param string $path
     */
    public function delete($spacename, $path){
        $params = array(
            'spacename' => $spacename,
            'path' => $path,
        );
        return $this->_do_request($this->_change_uri_to_url('/space/delete'), $params);
    }

    /**
     * 创建目录
     * @param string $spacename
     * @param string $dirname
     */
    public function make_dir($spacename, $dirname){
        $params = array(
            'spacename' => $spacename,
            'dirname' => $dirname,
        );
        return $this->_do_request($this->_change_uri_to_url('/space/makedir'), $params);
    }

    /**
     * 获取文件列表
     * @param string $spacename
     * @param string $dirname
     */
    public function get_list($spacename, $dirname){
        $params = array(
            'spacename' => $spacename,
            'dirname' => $dirname,
        );
        return $this->_do_request($this->_change_uri_to_url('/space/getList'), $params);
    }

    /**
     * 获取空间使用情况
     * @param string $spacename
     */
    public function get_space_usage($spacename){
        $params = array(
            'spacename' => $spacename,
        );
        return $this->_do_request($this->_change_uri_to_url('/space/getSpaceUsage'), $params);
    }

}