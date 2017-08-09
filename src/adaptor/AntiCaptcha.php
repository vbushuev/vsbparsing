<?php
namespace adaptor;
use core\Log as Log;
use core\HTTPConnector as Http;
class AntiCaptcha{
    protected $url = "http://rucaptcha.com/in.php";
    protected $url_res = "http://rucaptcha.com/res.php";
    protected $http;
    protected $key;
    public function __construct($key=false){
        if($key===false){
            include("config.php");
            $key = $cbConfig["rucaptcha"]["key"];
        }
        $this->key = $key;
        $this->http = new Http();
    }
    public function send($d){

    }
    public function get($d){
        $started = round(microtime(true) * 1000);
        $this->http->headers = ['Content-Type: multipart/form-data'];
        $res = $this->http->fetch($this->url,"POST",[
            "method"=>"base64",
            "key"=>$this->key,
            "body"=>base64_encode($d["captcha"]),
            "json"=>1
        ]);
        $response = json_decode($res,true);
        if(in_array($response["request"],["ERROR_IMAGE_TYPE_NOT_SUPPORTED","ERROR_ZERO_CAPTCHA_FILESIZE"])) return false;
        $r = $this->check($response["request"]);
        $ended = round(microtime(true) * 1000);
        Log::debug("Got (in ".($ended - $started)." ms) catpcha ".$ret."\n");
        return $r;
    }
    public function recaptcha($d){
        $started = round(microtime(true) * 1000);
        //http://rucaptcha.com/in.php?
            // key=1abc234de56fab7c89012d34e56fa7b8
            // &method=userrecaptcha
            // &googlekey=6Le-wvkSVVABCPBMRTvw0Q4Muexq1bi0DJwx_mJ-
            // &pageurl=http://mysite.com/page/with/recaptcha?appear=1&here=now
        $this->http->headers = ['Content-Type: multipart/form-data'];
        $res = $this->http->fetch($this->url,"POST",[
            "method"=>"userrecaptcha",
            "key"=>$this->key,
            "googlekey"=>$d["googlekey"],
            "pageurl"=>$d["pageurl"],
            "json"=>1
        ]);
        $response = json_decode($res,true);
        if($response["request"] == "ERROR_IMAGE_TYPE_NOT_SUPPORTED") return false;
        $r = $this->check($response["request"]);
        $ended = round(microtime(true) * 1000);
        Log::debug("Got (in ".($ended - $started)." ms) catpcha ".$ret."\n");
        return $r;
    }
    public function check($id){
        $got = false;
        $to = 2;
        while(!$got){
            $res = $this->http->fetch($this->url_res,"GET",[
                "key"=>$this->key,
                "id"=>$id,
                "json"=>1,
                "action"=>"get"
            ]);
            $res = json_decode($res,true);
            if($res["request"]=="ERROR_WRONG_CAPTCHA_ID")return false;
            $got = is_array($res) && isset($res["status"]) && ($res["status"]=="1");
            if(!$got)sleep($to);
        }
        return $res["request"];
    }
};
?>
