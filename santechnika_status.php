<?php
include("autoload.php");
use core\Log as Log;
use adaptor\opencart\Product as Product;
Log::$console=true;
$p = new Product;
$l = [];
try{
    $l = $p->get(["date_modified"=>">= date_add(now(),INTERVAL -3 MINUTE) "]);
}
catch(\Exception $e){$l=[];}
header('Content-Type: application/json; charset=utf-8');
echo json_encode(["count"=>count($l)],JSON_UNESCAPED_UNICODE);
?>
