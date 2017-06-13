<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Ebay as Ebay;
use adaptor\wordpress\woocommerce\Product as Product;
$tick = time();
Log::$console=true;

$ebay = new Ebay("T-shirt-Hoarders");
$products = $ebay->getProducts();

print_r($products);
foreach ($products as $product) new Product($product);
echo "\n---- script done in ".(time()-$tick)." ---- \n";
// iLU3V7s4Snyek2M4
?>
