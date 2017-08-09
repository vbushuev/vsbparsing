<?php
namespace core;
use core\Log;
class HTTPConnector extends Common{
    protected $results;
    protected $response;
    protected $cookies=[];
    protected $headers=[];
    protected $cookieFile ='';
    protected $config = [
        "method" => "GET",
        "proxy" => false,
        "json"=>false,
        "tor"=>false
    ];
    protected $curl = false;
    protected $responseHTTPCode = 200;
    protected $closed = true;
    public function getResponseCode(){return $this->responseHTTPCode;}
    public function __construct($a=[]){
        $localConf = Config::http();
        if(is_array($localConf))$this->config = array_merge($this->config,$localConf);
        if(is_array($a))$this->config = array_merge($this->config,$a);
        $this->curl = curl_init();
        $this->closed = false;
    }
    public function fetch($url,$m="GET",$d=[],$repeat=0){
        if($this->closed)$this->curl = curl_init();
        $host = parse_url($url);
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER=>$this->headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS =>20, // останавливаться после 10-ого редиректа
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_COOKIEJAR => 'cookie-jar',
            CURLOPT_COOKIEFILE => 'logs/cookie.txt',
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 0 //timeout in seconds
            //CURLOPT_VERBOSE => true,
            //CURLOPT_STDERR => fopen("curl.log",'w+')
        ];
        //$method = $_SERVER['REQUEST_METHOD'];
        if($this->config["proxy"]!==false){
            $curlOptions[CURLOPT_PROXY] = $this->getProxy();
        }

        if($m == 'POST'){
            $data = $this->config["json"]?json_encode($d):http_build_query($d);
            Log::debug("data{type:".($this->config["json"]?"json":"http").",value:".preg_replace(["/%5B/m","/%5D/m","/%40/m"],["[","]","@"],$data)."}");
            $curlOptions[CURLOPT_POST]=1;
            $curlOptions[CURLOPT_POSTFIELDS]=$data;
        }else {
            if(count($d)){
                $curlOptions[CURLOPT_URL].=(preg_match("/\?/",$curlOptions[CURLOPT_URL])?"&":"?").http_build_query($d);
            };
        }
        curl_setopt_array($this->curl, $curlOptions);
        $s = curl_exec($this->curl);
        $info = curl_getinfo($this->curl);

        $rheads = $this->headers;
        //Log::debug("HTTP/{$m} {$url}","Headers",$rheads,"Data",$d,"Response",$s);
        $this->responseHeaders(substr($s,0,$info["header_size"]));
        $response = substr($s,$info["header_size"]);
        $this->responseHTTPCode = intval($info["http_code"]);
        //if($this->responseHTTPCode!=200 && $repeat<3)$response = $this->fetch($url,$m,$d,$repeat++);
        return $response;
    }
    public function fetchMulti($urls,$m="GET",$d=""){
        if(!is_array($urls))$urls = [$urls];
        $response=[];
        $curls = [];
        $mh = curl_multi_init();
        foreach($urls as $url){
            $curls[$url] = curl_init();
            //$host = parse_url($url);
            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER=>$this->headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_MAXREDIRS =>20, // останавливаться после 10-ого редиректа
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => ""
            ];
            if($this->config["proxy"]!==false)$curlOptions[CURLOPT_PROXY] = $this->config["proxy"];
            if($m == 'POST'){
                $curlOptions[CURLOPT_POST]=1;
                $curlOptions[CURLOPT_POSTFIELDS]=$d;
            }
            curl_setopt_array($curls[$url], $curlOptions);
            curl_multi_add_handle($mh,$curls[$url]);
        }
        do{
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
        //$this->_properties["http_info"]=curl_multi_info_read($mh);
        // Obtendo dados de todas as consultas e retirando da fila
        foreach($curls as $url=>$curl){
            $response[$url]=curl_multi_getcontent($curl);
            $this->_properties["http_info"][$url] = curl_getinfo($curl);
            curl_multi_remove_handle($mh, $curl);
        }
        curl_multi_close($mh);
        return $response;
    }
    public function multiFetch($urls,callable $f){
        //if(!is_array($urls))$urls = [$urls];
        $response=[];
        $curls = [];
        $mh = curl_multi_init();
        foreach($urls as $url=>$arg){
            $curls[$url] = curl_init();
            //$host = parse_url($url);
            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER=>$this->headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_MAXREDIRS =>20, // останавливаться после 10-ого редиректа
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => ""
            ];
            if($this->config["proxy"]!==false){
                if(is_array($this->config["proxy"])){
                    $itr = isset($this->config["proxy_itr"])?$this->config["proxy_itr"]:0;
                    if(count($this->config["proxy"])<=$itr)$itr = 0;
                    $proxy = $this->config["proxy"][$itr]["url"];
                    $curlOptions[CURLOPT_PROXY] = $this->config["proxy"][$itr]["url"];
                    // $type = CURLOPT_PROXY_HTTP;
                    switch($this->config["proxy"][$itr]["type"]){
                        case "socks4":$curlOptions[CURLOPT_PROXYTYPE] = CURLOPT_PROXY_SOCKS4;break;
                        case "socks5":$curlOptions[CURLOPT_PROXYTYPE] = CURLOPT_PROXY_SOCKS5;break;
                    }
                    //$curlOptions[CURLOPT_PROXYTYPE] = $type;
                    ++$itr;
                    $this->config["proxy_itr"]=$itr;
                }
                //elseif(is_string($this->config["proxy"]))$proxy = $this->config["proxy"];

            }
            if($arg["method"] == 'POST'){
                $data = $this->config["json"]?json_encode($arg["data"]):http_build_query($arg["data"]);
                $curlOptions[CURLOPT_POST]=1;
                $curlOptions[CURLOPT_POSTFIELDS]=$data;
            }else {
                if(count($arg["data"])){
                    $curlOptions[CURLOPT_URL].=(preg_match("/\?/",$curlOptions[CURLOPT_URL])?"&":"?").http_build_query($arg["data"]);
                };
            }
            curl_setopt_array($curls[$url], $curlOptions);
            curl_multi_add_handle($mh,$curls[$url]);
        }
        do{
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
        //Log::debug("curl_multi_info: ",curl_multi_info_read($mh));
        // Obtendo dados de todas as consultas e retirando da fila
        foreach($curls as $url=>$curl){
            $calldata = [
                "url"=>$url,
                "request"=>$urls[$url],
                "response" => curl_multi_getcontent($curl),
                "http_info" => curl_getinfo($curl)
            ];
            //Log::debug($calldata);
            $f($calldata);
            //$this->_properties["http_info"][$url] = ;
            curl_multi_remove_handle($mh, $curl);
        }
        curl_multi_close($mh);
        return $response;
    }
    public function setHeaders($h=[]){
        $this->headers = $h;
    }
    public function getHeaders(){
        return $this->headers;
    }
    protected function responseHeaders($h){
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }
    }
    protected function getProxy(){
        if($this->config["tor"]){
            $tc = new \Dapphp\TorUtils\ControlClient();
            try {
                $tc->connect("127.0.0.1","9001"); // connect to 127.0.0.1:9051
                //$tc->authenticate('password');   // authenticate using hashedcontrolpassword "password"
                $tc->signal(\Dapphp\TorUtils\ControlClient::SIGNAL_NEWNYM);
                Log::Debug("Signal sent - IP changed successfully!");
            } catch (\Dapphp\TorUtils\ProtocolError $ex) {
                Log::debug( "Signal failed: ".$ex->getStatusCode());
            }
            catch (\Exception $ex) {
                Log::debug( "Signal failed: ".$ex->getMessage()."\ntrace:\n" . $ex->getTraceAsString());
            }
        }
        if(is_array($this->config["proxy"])){
            $itr = isset($this->config["proxy_itr"])?$this->config["proxy_itr"]:rand(0,count($this->config["proxy_itr"]));
            if(count($this->config["proxy"])<=$itr)$itr = 0;
            $proxy = $this->config["proxy"][$itr]["url"];

            // $type = CURLOPT_PROXY_HTTP;
            switch($this->config["proxy"][$itr]["type"]){
                case "socks4":$curlOptions[CURLOPT_PROXYTYPE] = CURLOPT_PROXY_SOCKS4;break;
                case "socks5":$curlOptions[CURLOPT_PROXYTYPE] = CURLOPT_PROXY_SOCKS5;break;
            }
            //$curlOptions[CURLOPT_PROXYTYPE] = $type;
            Log::debug("using proxy: ".$this->config["proxy"][$itr]["url"]);
            ++$itr;
            $this->config["proxy_itr"]=$itr;


            return $this->config["proxy"][$itr]["url"];
        }
        //elseif(is_string($this->config["proxy"]))$proxy = $this->config["proxy"];
    }
    public function close(){
        curl_close($this->curl);
        $this->closed = true;
    }
    public static function parseQuery($s){
        $resp = [];
        if(preg_match_all("/([^=]+)=([^\&]+)&?/uim",$s,$m)){
            for($i=0;$i<count($m[0]);++$i){
                $resp[$m[1][$i]] = urldecode($m[2][$i]);
            }
        }
        return $resp;
    }
};
?>
