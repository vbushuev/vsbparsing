<?php
namespace carbazar;
use core\Log as Log;
class Auth {
    protected $resp=[
        "code"=>200,
        "status"=>"success",
        "message"=>"",
        "request"=>[],
        "response"=>[]
    ];
    public function __construct($a=null){
        if(!is_null($a) && is_array($a)){
            if(isset($a["session"])){
                try{
                    $session = new Session($a);
                    $apikey = new Apikey;
                    $apikey->find(["id"=>$session->apikey_id]);
                    $client = new Client;
                    $client->find(["id"=>$apikey->client_id]);
                }
                catch(\Exception $e){
                    $message = "unknown session #".$a["session"];
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
                $client = new Client;
                try{
                    $client->find(['login'=>$a["login"]]);
                    if($client->password != md5($a["password"])){
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
                    $apikey = new Apikey;
                    try{
                        $apikey->find(["apikey"=>$a["apikey"],"client_id"=>$client->id]);
                        try{
                            $session = new Session(["apikey_id"=>$apikey->id]);
                            $this->resp["response"]= $session->toArray();
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
