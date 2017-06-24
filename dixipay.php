<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
Log::$console=true;

$h = new Http();
echo $h->fetch("https://buycard.org/ru/wc-api/WC_Gateway_tmgpay","POST",["ticketNumber"=>"480","responseCode"=>"A01"]);
exit;


$auth_data = [
    'apiKey'=>'135a376b-41ae-4351-b2ad-076ac808e65b',
    'userName'=>'testApi',
    'password'=>'S33cFb2EPPjbDM7Uh5zX0Kbp2S6F5sok',//'E845VinLNLTunjNpe2LcUtl7hGfs6H9j',
    'merchantAccountCode' => '300042',
    "customerAccountCode"=>'25052505'
];
if(isset($argv[1]) && $argv[1] == "export"){
    $url = "https://lk.dixipay.eu/gates/xurl";
    $request = array_merge($auth_data,[
        'requestType' =>'export',
        'zip'=>'N',
        'fromRequestDate'=>'20170601',
        'toRequestDate'=>'20170623'
    ]);
    $response = $h->fetch($url,"GET",$request);
    echo "REQUEST:\n{$url}\n".http_build_query($request)."\n\n";
    echo "RESPONSE:\n{$response}\n";
    file_put_contents("export.csv",$response);
}
else{
    $url = "https://lk.dixipay.eu/gates/signature";
    $request = array_merge($auth_data,[
        'requestType' =>'sale-auth',
        'ticketNumber'=> '151251234',
        'transactionIndustryType'=>'EC',
        'amount'=>"5300",
        'transactionIndustryType'=>'EC',
        //'transactionCode'=>'000000002353',
        "accountType"=>"R",
        "currency"=>'USD',
        "lang"=>'RU',
        "memo"=>"xyz",
    	"itemCount"=>"2",
    	"items"=>"(code=167;itemNumber=167;description=Товар;quantity=1;price=54995;unitCostAmount=54995;totalAmount=54995)",
    	"holderType"=>"P",
    	"holderName"=>"MAX+PAX",
    	"holderBirthdate"=>"19690525",
    	"street"=>"ул. Ленина 102",
    	"city"=>"Москва",
    	"zipCode"=>"30301",
    	"phone"=>"7925856324",
    	"email"=>"mail@mail.ru"
    ]);
    $response = $h->fetch($url,"GET",$request);
    echo "REQUEST:\n{$url}\n".http_build_query($request)."\n\n";
    $resp= Http::parseQuery($response);
    $action = $resp["action"];
    if(isset($resp["action"])){
        echo "\nhttps://lk.dixipay.eu/gates/paypage?action=".urlencode($action)."\n";
    }
    else echo "RESPONSE:\n{$response}\n";



}





// $request["action"] = $resp["action"];
// $request['requestType'] ='sale';
// //print_r($resp);
//$resp= $h->fetch("https://lk.dixipay.eu/gates/paypage","GET",["action"=>$action]);
//print_r($resp);
?>
