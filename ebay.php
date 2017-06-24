<?php
chdir(dirname(__FILE__));
set_time_limit(600);
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Ebay as Ebay;
use adaptor\wordpress\woocommerce\Product as Product;
$tick = time();
Log::$console=true;

$ebay = new Ebay("T-shirt-Hoarders");
$products = $ebay->getProducts(function($prd){
    $product = new Product($prd);
    Log::console("from ",$prd->toJSON()," to ",$product->toJSON());
});
//
// print_r($products);
// foreach ($products as $product)
echo "\n---- script done in ".(time()-$tick)." ---- \n";
// iLU3V7s4Snyek2M4
?>
