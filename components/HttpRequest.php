<?php
namespace app\components;

class HttpRequest {

    protected $hasCurl;
    protected $useCurl;
    protected $useBasicAuth;
    protected $HTTPVersion;
    protected $referer;
    protected $url;
    protected $host;
    protected $port;
    protected $uri;
    protected $type;
    protected $query;
    protected $timeout;
    protected $error;
    protected $response;
    protected $responseText;
    protected $responseHeaders;
    protected $executed;
    protected $fsock;
    protected $curl;
    protected $additionalCurlOpts;
    protected $authUsername;
    protected $authPassword;


    public function __construct($host = null, $uri = '/', $type='GET',$port = 80, $useCurl = null,$timeout = 10) {
        $this -> hasCurl = function_exists('curl_init');
        $this -> useCurl = $this -> hasCurl ? ($useCurl !== null ? $useCurl : true) : false;
        $this -> type = $type;
        $this -> HTTPVersion = '1.1';
        $this -> host = $host ? $host : $_SERVER['HTTP_HOST'];
        $this -> uri = $uri;
        $this -> port = $port;
        $this -> timeout = $timeout;
        $this -> setHeader('cache-control', 'no-cache');
        $this -> setHeader('Postman-Token', '9f4f0b97-da40-4348-b6ba-b25f21fb646d');
//        $this -> setHeader('Accept-Encoding', 'deflate');
//        $this -> setHeader('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
//        $this -> setHeader('User-Agent', 'Mozilla/5.0 Firefox/3.6.12');
//        $this -> setHeader('Connection', 'close');
        return $this;
    }

    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @return array  $data   响应数据
     */
    public static function net_req($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        var_dump($opts);exit;
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }

    public function setHost($host) {
        $this -> host = $host;
        return $this;
    }

    public function setRequestURI($uri) {
        $this -> uri = $uri;
        return $this;
    }

    public function setPort($port) {
        $this -> port = $port;
        return $this;
    }

    public function setTimeout($timeout) {
        $this -> timeout = $timeout;
        return $this;
    }

    public function setGetData($get) {

        $this -> query = $get;
        return $this;
    }

    public function setQueryParams($get) {
        $this -> query = $get;
        return $this;
    }

    public function setUseCurl($use) {
        if ($use && $this -> hasCurl) {
            $this -> useCurl = true;
        } else {
            $this -> useCurl = false;
        }
        return $this;
    }

    public function setType($type) {
        if (in_array($type, array('POST', 'GET', 'PUT', 'DELETE'))) {
            $this -> type = $type;
        }
        return $this;
    }

    public function setData($data) {
        $data && $this -> data = $data;
        return $this;
    }

    public function param($data) {
        if(is_string($data)) return $data;
        $data_array = array();
        foreach ($data as $key => $val) {
            if (!is_string($val)) {
                $val = json_encode($val);
            }
            $data_array[] = urlencode($key).'='.urlencode($val);
        }
        return implode('&', $data_array);
    }
    public function paramJson($data) {
        if(is_string($data)) return $data;
        return json_encode($data);
    }
    public function setUrl($url) {
        if(!$url) return $this;

        $this-> url = $url;
        $url_info = parse_url($url);
        !isset($url_info['scheme']) && $url_info['scheme'] = 'http';
        $this->port = strtolower($url_info['scheme']) == 'https' ? 443 : (isset($url_info['port']) ? $url_info['port'] : 80);
        $this->host = $url_info['host'];
        $this->uri = $url_info['path'];
        isset($url_info['query']) && $this->setGetData($url_info['query']);
        return $this;
    }

    public function setHeader($header, $content) {
        $this -> headers[$header] = $content;
        return $this;
    }

    public function setReferer($referer) {
        $this-> referer = $referer;
        return $this;
    }

    public function setAdditionalCurlOpt($option, $value) {
        if (is_array($option)) {
            foreach ($option as $opt => $val) {
                $this -> setAdditionalCurlOpt($opt, $val);
            }
        } else {
            $this -> additionalCurlOpts[$option] = $value;
        }
    }

    public function setUseBasicAuth($set, $username = null, $password = null) {
        $this -> useBasicAuth = $set;
        if ($username) {
            $this -> setAuthUsername($username);
        }
        if ($password) {
            $this -> setAuthPassword($password);
        }
    }

    public function setAuthUsername($username = null) {
        $this -> authUsername = $username;
    }

    public function setAuthPassword($password = null) {
        $this -> authPassword = $password;
    }

    public function execute() {
        if ($this -> useCurl) {
            $this -> curl_execute();
        } else {
            $this -> fsockget_execute();
        }
        return $this;
    }

    protected function curl_execute() {
        $uri = $this -> uri;
        $host = $this -> host;
        $type = $this -> type;
        $port = $this -> port;
        $data = property_exists($this, 'data') ? $this -> data : false;

        $timeout = $this -> timeout;

        $ch = curl_init();
        // Set request type.
        if ($type === 'GET') {
            $this->setGetData($data);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else if ($type === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } else if ($type === 'PUT') {
            curl_setopt($ch, CURLOPT_PUT, true);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
        // Grab query string.
        $query = property_exists($this, 'query') && $this -> query ? '?'.self::param($this -> query) : '';
// echo $query;exit;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Set additional headers.
        $headers = array();
        foreach ($this -> headers as $name => $val) {
            $headers[] = $name.': '.$val;
        }
//        var_dump($headers);exit;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Do stuff it it's HTTPS/SSL.
        if ($port == 443) {
            $protocol = 'https';
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $protocol = 'https';
        }

        if (!empty($this -> additonalCurlOpts)) {
            foreach ($this -> additionalCurlOpts as $option => $value) {
                curl_setopt($ch, $option, $value);
            }
        }
        // Build and set URL.
        $url = $protocol.'://'.$host.$uri.$query;
//        echo $url;exit;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $port);
//        print_r($ch);exit;


        // Add any authentication to the request.
        // Currently supports only HTTP Basic Auth.
        if ($this -> useBasicAuth === true) {
            curl_setopt($ch, CURLOPT_USERPWD, $this -> authUsername.':'.$this -> authPassword);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        if ($this->referer) {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }
        // Execute!
        $rsp = curl_exec($ch);
        var_dump($rsp);
        var_dump(curl_error($ch));exit;
        $this -> curl = $ch;
        $this -> executed = true;
        // Handle an error.
        if (!$error = curl_error($ch)) {
            $this -> response = array('responseText' => $rsp) + curl_getinfo($ch);
            $this -> responseHeaders = curl_getinfo($ch);
            $this -> responseText = $rsp;
        } else {
            $this -> error = $error;
        }
    }

    protected function fsockget_execute() {
        $uri = $this -> uri;
        $host = $this -> host;
        $port = $this -> port;
        $type = $this -> type;
        $HTTPVersion = $this -> HTTPVersion;
        $data = property_exists($this, 'data') ? $this -> data : null;
        $crlf = "\r\n";

        $rsp = '';
// var_dump($data);exit;

        // Deal with the data first.
        if ($data && $type === 'POST') {
            $data = $this -> param($data);
        } else if ($data && $type === 'GET') {
            $get_data = $data;
            $data = $crlf;
        } else {
            $data = $crlf;
        }
        // Then add
        if ($type === 'POST') {
            if(!$this -> headers['Content-Type']){
                $this -> setHeader('Content-Type', 'application/x-www-form-urlencoded');
            }
            $this -> setHeader('Content-Length', strlen($data));
            $get_data = property_exists($this, 'query') && $this -> query ? self::param($this -> query) : false;
        } else {
            if(!$this -> headers['Content-Type']){
                $this -> setHeader('Content-Type', 'text/plain');
            }
            $this -> setHeader('Content-Length', strlen($crlf));
        }
        if ($type === 'GET') {
            if (isset($get_data)) {
                $get_data = $data;
            } else if ($this -> query) {
                $get_data = self::param($this -> query);
            }
        }
        if ($this -> useBasicAuth === true) {
            $this -> setHeader('Authorization', 'Basic '.base64_encode($this -> authUsername.':'.$this -> authPassword));
        }
// $headers = $this -> headers;

        $req = '';
        $req .= $type.' '.$uri.(isset($get_data) ? '?'.$get_data : '').' HTTP/'.$HTTPVersion.$crlf;
        $req .= "Host: ".$host.$crlf;
        foreach ($this -> headers as $header => $content) {
            $req .= $header.': '.$content.$crlf;
        }
        $req .= $crlf;
        if ($type === 'POST') {
            $req .= $data;
        } else {
            $req .= $crlf;
        }

        // Construct hostname.
        $fsock_host = ($port == 443 ? 'ssl://' : '').$host;
        // Open socket.
        $httpreq = @fsockopen($fsock_host, $port, $errno, $errstr, 30);

        // Handle an error.
        if (!$httpreq) {
            $this -> error = $errno.': '.$errstr;
            return false;
        }

        // Send the request.
        fputs($httpreq, $req);

        // Receive the response.
        while($line = fgets($httpreq)) {
            $rsp .= $line;
        }
// while (!feof($httpreq))
// {
// $rsp .=@fgets($httpreq,4096);
// }

// print_r($rsp);exit;

        // Extract the headers and the responseText.
        list($headers, $responseText) = explode($crlf.$crlf, $rsp);

        // Store the finalized response.
        $this -> response = $rsp;
        $this -> responseText = $responseText;
// $this -> status = array_shift($headers);

        // Store the response headers.
        $headers = explode($crlf, $headers);
        $this -> responseHeaders = array();
        foreach ($headers as $header) {
            list($key, $val) = explode(': ', $header);
            $this -> responseHeaders[$key] = $val;
        }
        // Mark as executed.
        $this -> executed = true;

        // Store the resource so we can close it later.
        $this -> fsock = $httpreq;
    }

    public function close() {
        if (!$this -> executed) {
            return false;
        }
        if ($this -> useCurl) {
            $this -> curl_close();
        } else {
            $this -> fsockget_close();
        }
    }

    protected function fsockget_close() {
        fclose($this -> fsock);
    }

    protected function curl_close() {
        curl_close($this -> curl);
    }

    public function getError() {
        return $this -> error;
    }

    public function getResponse() {
        if (!$this -> executed) {
            return false;
        }
        return $this -> response;
    }

    public function getResponseText() {
        if (!$this -> executed) {
            return false;
        }
        return $this -> responseText;
    }

    public function getAllResponseHeaders() {
        if (!$this -> executed) {
            return false;
        }
        return $this -> responseHeaders;
    }

    public function getResponseHeader($header) {
        if (!$this -> executed) {
            return false;
        }
        $headers = $this -> responseHeaders;
        if (array_key_exists($header, $headers)) {
            return $headers[$header];
        }
    }
    public static function curlHeaders() {
        return array(
            'User-Agent' => CURLOPT_USERAGENT,
        );
    }
}