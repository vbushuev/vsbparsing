<?php
include("autoload.php");
use core\Log as Log;
use adaptor\yml\Reader as YMLReader;
use adaptor\joomla\ksenmart\Category as Category;
use adaptor\joomla\ksenmart\Product as Product;
$yml ="";
if(!file_exists('input/standart_yml_catalog.xml.'.date('Y-m-d'))){
    $yml = file_get_contents('https://tytmodno.com/standart_yml_catalog.xml');
    file_put_contents('input/standart_yml_catalog.xml.'.date('Y-m-d'),$yml);
}else $yml = file_get_contents('input/standart_yml_catalog.xml.'.date('Y-m-d'));
$yml = file_get_contents('input/standart_yml_catalog.xml');
$reader = new YMLReader($yml);
$categories = $reader->getCategories();
foreach ($categories as $key => $value) {
    $categories[$k] = new Category($value);
}
$products = $reader->getProducts('RUR');
foreach ($products as $product) {
    $product->category_id = $categories[$product->category_id]->id;
    new Product($product);
}
?>
