<?php
namespace cb;
use core\Config as Config;
use core\Log as Log;
use core\HTTPConnector as Http;
class Unisend{
    protected $_cfg=[];
    protected $_fields=["email","city","vin","id-obyav","region","price","year","model","marka","oplachen-do","balance","terif","id-company","Name","reg-date"];
    public function __construct(){
        $this->_cfg = Config::unisend();
    }
    public function addContact($d){
        if(!isset($d["email"]))return[];
        $req=[];
        $result = [];
        $result[] = $this->importContacts([
            "field_names"=>["email","email_list_ids","email_request_ip"],
            "data"=>[
                [$d["email"],$this->_cfg["list_ids"],Config::IP()]
            ]
        ]);
        $data = [
            "list_ids"=>$this->_cfg["list_ids"],
            "request_ip"=>(Config::IP()=="127.0.0.1")?"94.25.177.157":Config::IP(),
            "request_time"=>date("Y-m-d"),
            "overwrite"=>2,
            "double_optin"=>3,
            "fields"=>["email"=>$d["email"]]
        ];
        if(isset($d["vin"]))$data["fields"]["vin"]=$d["vin"];
        if(isset($d["city"]))$data["fields"]["city"]=$d["city"];
        if(isset($d["region"]))$data["fields"]["region"]=$d["region"];
        if(isset($d["model"]))$data["fields"]["model"]=$d["model"];
        if(isset($d["brand"]))$data["fields"]["marka"]=$d["brand"];
        if(isset($d["cb_order_id"]))$data["fields"]["id_vin"]=$d["cb_order_id"];
        if(isset($d["year"]))$data["fields"]["year"]=$d["year"];
        $result[] = $this->subscribe($data);
        return $result;
    }
    public function __call($f,$a){
        return $this->call($f,isset($a[0])?$a[0]:[]);
    }
    protected function call($method,$data){
        $res = [];
        try {
            $req = array_merge([
                "format"=>"json",
                "api_key"=>$this->_cfg["api_key"],
            ],$data);
            $http = new Http();
            $http->headers = ["Accept"=>"application/json, text/javascript, */*; q=0.01"];
            $res = $http->fetch($this->_cfg["host"]."/{$method}","POST",$req);
        }
        catch(\Exception $e){
            Log::debug($e);
        }
        return json_decode($res,true);
    }
};
?>
