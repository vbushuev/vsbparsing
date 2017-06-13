<?php
include("autoload.php");
use core\Log as Log;
use adaptor\yml\Reader as YMLReader;
use adaptor\joomla\ksenmart\Category as Category;
use adaptor\joomla\ksenmart\Product as Product;
Log::$console = true;
Log::debug("service started");
try{
    $yml ="";
    if(!file_exists('input/standart_yml_catalog.xml.'.date('Y-m-d'))){
        $yml = file_get_contents('https://tytmodno.com/standart_yml_catalog.xml');
        file_put_contents('input/standart_yml_catalog.xml.'.date('Y-m-d'),$yml);
    }else $yml = file_get_contents('input/standart_yml_catalog.xml.'.date('Y-m-d'));
    //$yml = file_get_contents('input/standart_yml_catalog.xml');
    //$yml = file_get_contents('https://tytmodno.com/standart_yml_catalog.xml');
    Log::debug("yml getted: ".strlen($yml));
    $reader = new YMLReader($yml);
    Log::debug("yml readed ");
    $categories = $reader->getCategories();
    Log::debug("categories:",json_encode($categories));
    foreach ($categories as $key => $value) {
        $categories[$key] = new Category($value);
    }
    $products = $reader->getProducts('RUR');

    foreach ($products as $product) {
        //Log::debug("Loading product ".$product->toJSON());
        $product->category_id = $categories[$product->category_id]->id;
        new Product($product);
    }
}
catch(\Exception $e){
    Log::debug($e->__toString());
}
Log::debug("service finished");
/*
delete from o61c7_ksenmart_product_properties_values where o61c7_ksenmart_product_properties_values.product_id > 28662;
delete from o61c7_ksenmart_products_categories where product_id>28662;
delete from `o61c7_ksenmart_files` WHERE owner_id > 28662 and owner_type ='product';
delete from `o61c7_ksenmart_products` WHERE id > 28662;
*/
?>
