<?php
namespace carbazar\parse;
use core\Log as Log;
use core\HTTPConnector as Http;
use adaptor\AntiCaptcha as Captcha;

class Zalog{
    public function get($d){
        $res = "{}";
        $request = [
            "VIN" => $d["vin"],
            "formName"=>'vehicle-form',
            "uuid"=>''
        ];
        Log::debug("Zalog request ",$request);
        try{
            $url = "https://www.reestr-zalogov.ru/search/index";
            $http = new Http();
            $captcha = new Captcha();
            $html = $http->fetch($url);
            $uuid = 'b6fa0009-2777-461c-94b1-7482368990dc';
            //<input type="hidden" id="uuid" name="uuid" value="b6fa0009-2777-461c-94b1-7482368990dc">
            // #recaptcha > div > div.grecaptcha-logo > iframe
            // https://www.google.com/recaptcha/api2/anchor?k=6LfOhRwUAAAAADB8NfThHfVsEWYbTf8oXYlwT9W4&co=aHR0cHM6Ly93d3cucmVlc3RyLXphbG9nb3YucnU6NDQz&hl=ru&v=r20170613131236&size=invisible&cb=vu7582l97x9p
            if(preg_match("/id=\"uuid\"\s+name=\"uuid\"\s+value=\"([^\"]+)\"/im",$html,$m)){
                $uuid = $m[1];
                $d["uuid"] = $m[1];
                //echo "FOUND UUID:".$uuid;
            }
            //$d["captcha"] = $http->fetch("https://www.reestr-zalogov.ru/captcha/generateCaptcha?".$this->random());
            //file_put_contents("store/zalog_captcha.jpg",$d["captcha"]);
            // solve captcha
            if(preg_match("/www\.google\.com\/recaptcha\/api2\/anchor\?k=([^\&])/im",$html,$m))$d["googlekey"] = $m[1];
            elseif( preg_match ("/data\-sitekey=[\"'](.+?)[\"']/i",$html,$m)) $d["googlekey"] = $m[1];
            elseif( preg_match ("/window\.recaptcha\s?=\s?\{\s*site:\s*\"([^\"]+)\"/im",$html,$m)) $d["googlekey"] = $m[1];
            $d["pageurl"] = $url;

            $word = $captcha->recaptcha($d);
            //print_r($word);exit;
            if($word===false) return $res;
            // checkdata http://check.gibdd.ru/proxy/check/auto/history
            $request = [
                "VIN" => $d["vin"],
                "token"=>$word,
                "formName"=>'vehicle-form',
                "uuid"=>$uuid
            ];
            $res = $http->fetch("https://www.reestr-zalogov.ru/search/endpoint","POST",$request);
            $http->close();
        }
        catch(\Exception $e){
            Log::debug($e);
        }
        Log::debug("Zalog response ",$res);
        return $res;
    }
    protected function random(){
        return round(microtime(true) * 1000)."";
    }
};
?>
