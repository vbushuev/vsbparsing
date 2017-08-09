<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . '/home/srv42489/domsans72.ru/pars/santechnika/');
// echo preg_replace("/([^\/]+)$/","src/$1","/Dapphp/TorUtils/ControlClient")."\n";exit;
chdir(dirname(__FILE__));
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Santehnika as Santehnika;
use adaptor\opencart\Category as Category;
use adaptor\opencart\Product as Product;
$include = (isset($argv)&&(count($argv)>1)&&file_exists($argv[1]))?$argv[1]:"catalogs.php";
include $include;

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
// DcWpH3P2
/*
root: mv8txQA387RtTdU9
Имя базы	fastuser_db
Пользователь	fastuser_usr
Пароль	lWTXNHaa9YCXRXuP

FTP	ftp://s052d746efastvps_servercom462:4ceab765cc27@5.45.116.110
Логин	s052d746efastvps_servercom462
Пароль	4ceab765cc27
*/
?>
