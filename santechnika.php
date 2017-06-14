<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Santehnika as Santehnika;
use adaptor\opencart\Category as Category;
use adaptor\opencart\Product as Product;
include("catalogs.php");
$tick = time();
Log::$console=true;
$source = new Santehnika();
$categories = $source->getCategories(
    $cats,
    // [[
    //     "id"=>"mebel_dlya_vannoy_ot_40_do_50_sm",
    //     //"title"=>"Мебель для ванной\Мебель 40-50см",
    //     "title"=>"Мебель 40-50см",
    //     "url"=>"http://santehnika-online.ru/mebel_dlja_vannoj_komnaty/mebel_dlya_vannoy_ot_40_do_50_sm/",
    //     "brands"=>[
    //         "Aqwella",
    //         "Aqwella 5 stars",
    //         "ArtCeram",
    //         "BelBagno",
    //         "Cezares",
    //         "Clarberg",
    //         "Dreja",
    //         "Duravit",
    //         "Edelform",
    //         "Ingenium",
    //         "Jacob Delafon",
    //         "Kerasan",
    //         "Laufen",
    //         "Migliore",
    //         "Roca",
    //         "Акватон"
    //     ]
    // ]],
    function($calldata){
        $ret = new Category($calldata);
        //Log::console($calldata->toJSON(),$ret->toJSON());
        //print_r($calldata);
        //print_r($ret);
        return $ret;
    }
);
$products = $source->getProducts(function($prd){
    $newProd = new Product($prd);
    //exit;
    //Log::console($prd->toJSON(),$newProd->toJSON());
});
//print_r($products);
//foreach ($products as $product) new Product($product);
echo "\n---- script done in ".(time()-$tick)." ---- \n";
// iLU3V7s4Snyek2M4
?>
