<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
//Log::$console=true;

$h = new Http();
echo $h->fetch("http://dixipay.bs2/wc-api/WC_Gateway_dixipay","POST",["transactionCode"=>"1223","responseCode"=>"A01"]);
exit;

$url = "https://lk.dixipay.eu/gates/signature";

$request = [
    'requestType' =>'sale',

    'apiKey'=>'135a376b-41ae-4351-b2ad-076ac808e65b',
    'userName'=>'testApi',
    'password'=>'E845VinLNLTunjNpe2LcUtl7hGfs6H9j',
    'merchantAccountCode' => '300042',
    "customerAccountCode"=>'25052505',

    // 'apiKey'=>'b1062dd7-49b8-45aa-8b14-14b11d43d7e2',
    // 'userName'=>'apiBuycard',
    // 'password'=>'49Vi723T34GN7i233TzQBlP3K2t2SO0L',
    // 'merchantAccountCode' => '658000',
    // "customerAccountCode"=>'658001',


        'ticketNumber'=> '15125125',
        'transactionIndustryType'=>'EC',


        'amount'=>$amount,
        'transactionIndustryType'=>'EC',
        'transactionCode'=>'000000002353',


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
    //
    //
    // 'accountType'=>'R',
    // 'accountNumber'=>'4111111111111111',
    // 'accountAccessory'=>'1020',
    // 'cvv2'=>'123',
    // 'holderName'=>'Ali Baba',
    // 'token'=>'VC84632147254611111111'
];
$response = $h->fetch($url,"GET",$request);
echo "REQUEST:\nhttps://portal.tmgpay.eu/gates/signature\n".http_build_query($request)."\n\n";
echo "RESPONSE:\n{$response}\n";
$resp= Http::parseQuery($response);
$action = $resp["action"];


// $request["action"] = $resp["action"];
// $request['requestType'] ='sale';
// //print_r($resp);
//$resp= $h->fetch("https://lk.dixipay.eu/gates/paypage","GET",["action"=>$action]);
//print_r($resp);
?>
