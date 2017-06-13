<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
//Log::$console=true;

$h = new Http();
$url = "https://lk.dixipay.eu/gates/signature";

$request = [
    'requestType' =>'sale',
    // 'requestType' =>'merchant-info',
    'merchantAccountCode' => '300042',
    // 'userName'=>'apiTMG',
    // 'password'=>'2sgJUF7IHKb4ip7EkA27FEEtCDL4iKDg',
    'ticketNumber'=>'0004',

    'amount'=>'12300',
    'transactionIndustryType'=>'EC',
    'transactionCode'=>'000000002353',
    'apiKey'=>'135a376b-41ae-4351-b2ad-076ac808e65b',

    "accountType"=>"R",
	"currency"=>"USD",
	"lang"=>"EN",
	"customerAccountCode"=>"25052505",

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
echo "{$response}\n";
$resp= Http::parseQuery($response);
$action = $resp["action"];


// $request["action"] = $resp["action"];
// $request['requestType'] ='sale';
// //print_r($resp);
$resp= $h->fetch("https://lk.dixipay.eu/gates/paypage","GET",["action"=>$action]);
print_r($resp);
?>
