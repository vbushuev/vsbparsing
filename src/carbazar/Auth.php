<?php
namespace carbazar;
use core\Log as Log;
class Auth {
    protected $session= null;
    protected $client = null;
    protected $apikey = null;
    protected $resp=[
        "code"=>200,
        "status"=>"success",
        "message"=>"",
        "request"=>[],
        "response"=>[]
    ];
    public function getClient(){return $this->client;}
    public function getSession(){return $this->session;}
    public function getApikey(){return $this->apikey;}
    public function __construct($a=null){
        if(!is_null($a) && is_array($a)){
            if(isset($a["session"])){
                try{
                    $this->session = new Session($a);
                    $this->client = new Client;
                    $this->client->find(["id"=>$this->session->client_id]);
                    $this->apikey = new Apikey;
                    Log::debug($this->session->apikey_id,$this->client->account_id);
                    $this->apikey->find(["id"=>$this->session->apikey_id,"account_id"=>$this->client->account_id]);
                    if($this->apikey->quantity<=0){
                        $message = "not enough requests";
                        Log::debug( $message );
                        $this->resp = [
                            "code"=>403,
                            "status"=>"auth failed",
                            "message"=>$message,
                            "request"=>$a,
                            "response"=>[]
                        ];
                    }else{
                        $this->session->update();
                    }
                }
                catch(\Exception $e){
                    $message = "unknown or expired session #".$a["session"];
                    Log::debug( $message );
                    $this->resp = [
                        "code"=>401,
                        "status"=>"auth failed",
                        "message"=>$message,
                        "request"=>$a,
                        "response"=>[]
                    ];
                }
            }
            else{
                $this->client = new Client;
                try{
                    $this->client->find(['login'=>$a["login"]]);
                    if($this->client->password != md5($a["password"])){
                        $message = "wrong password #".$a["login"];
                        Log::debug( $message );
                        $this->resp = [
                            "code"=>401,
                            "status"=>"auth failed",
                            "message"=>$message,
                            "request"=>$a,
                            "response"=>[]
                        ];
                        return;
                    }
                    $this->apikey = new Apikey;
                    try{
                        $this->apikey->find(["apikey"=>$a["apikey"],"account_id"=>$this->client->account_id]);
                        try{
                            $this->session = new Session(["apikey_id"=>$this->apikey->id,"client_id"=>$this->client->id]);
                            $this->resp["response"]= $this->session->toArray();
                        }
                        catch(\Exception $e){
                            $message = $e->getMessage();
                            Log::debug( $message );
                            $this->resp = [
                                "code"=>401,
                                "status"=>"auth failed",
                                "message"=>$message,
                                "request"=>$a,
                                "response"=>[]
                            ];
                        }
                    }
                    catch(\Exception $e){
                        $message = "wrong apikey #".$a["apikey"];
                        Log::debug( $message );
                        $this->resp = [
                            "code"=>401,
                            "status"=>"auth failed",
                            "message"=>$message,
                            "request"=>$a,
                            "response"=>[]
                        ];
                    }
                }
                catch(\Exception $e){
                    $message = "unknown login #".$a["login"];
                    Log::debug( $message );
                    $this->resp = [
                        "code"=>401,
                        "status"=>"auth failed",
                        "message"=>$message,
                        "request"=>$a,
                        "response"=>[]
                    ];
                }
            }
        }
    }
    public function getResp(){
        return $this->resp;
    }
};
?>
