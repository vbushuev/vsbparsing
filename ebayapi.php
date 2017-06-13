<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
Log::$console=false;
$ebayApiID = "Vladimir-itemsfin-SBX-37a9b4557-5526d0c1";
$ebayApiUrl = "http://svcs.sandbox.ebay.com/services/search/FindingService/v1";
$h = new Http();
$q = [
    "OPERATION-NAME"=>"findItemsByCategory",
    "SERVICE-VERSION"=>"1.0.0",
    "SECURITY-APPNAME"=>$ebayApiID,
    "RESPONSE-DATA-FORMAT"=>"JSON",
    "categoryId"=>"94709465",
    "paginationInput.entriesPerPage"=>100,
    "paginationInput.pageNumber"=>1
];
$q = [
    "OPERATION-NAME"=>"findItemsByKeywords",
    "SERVICE-VERSION"=>"1.0.0",
    "SECURITY-APPNAME"=>$ebayApiID,
    "RESPONSE-DATA-FORMAT"=>"JSON",
    "keywords"=>"T-shirt-Hoarders",
    "paginationInput.entriesPerPage"=>100,
    "paginationInput.pageNumber"=>1
];

$r = $h->fetch($ebayApiUrl,"get",$q);
echo $r."\n";

?>
