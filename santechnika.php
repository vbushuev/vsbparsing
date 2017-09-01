<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . '/home/srv42489/domsans72.ru/pars/santechnika/');
// echo preg_replace("/([^\/]+)$/","src/$1","/Dapphp/TorUtils/ControlClient")."\n";exit;
chdir(dirname(__FILE__));
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Dushevoi as Dushevoi;
use adaptor\opencart\Category as Category;
use adaptor\opencart\Product as Product;
$include = (isset($argv)&&(count($argv)>1)&&file_exists($argv[1]))?$argv[1]:"catalogs.php";
include $include;

$tick = time();
Log::$console=true;
$source = new Dushevoi();
$categories = $source->getCategories(
    $cats,
    function($calldata){
        return new Category($calldata);
    }
);
$products = $source->getProducts(function($prd){
    $newProd = new Product($prd);
    exit;
    // Log::console($prd->toJSON());
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
