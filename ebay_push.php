<?php
chdir(dirname(__FILE__));
include("autoload.php");
use source\Ebay as Ebay;
$tick = time();
$ebay = new Ebay("T-shirt-Hoarders");
try{
    $ebay->push();
}
catch(\Exception $e){
    echo $e->getMessage()."\n";
}
echo "\n---- script Pushing done in ".(time()-$tick)." ---- \n";
?>
