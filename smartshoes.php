<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . '/home/srv42489/domsans72.ru/pars/santechnika/');
// echo preg_replace("/([^\/]+)$/","src/$1","/Dapphp/TorUtils/ControlClient")."\n";exit;
chdir(dirname(__FILE__));
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Smartshoes as Smartshoes;
use adaptor\opencart\Category as Category;
use adaptor\opencart\Product as Product;
$include = (isset($argv)&&(count($argv)>1)&&file_exists($argv[1]))?$argv[1]:"input/smcats.php";
include $include;

$tick = time();
// Log::$console=true;
$source = new Smartshoes();
$categories = $source->getCategories(
    $cats,
    function($calldata){
        $ret= new Category($calldata);
        // Log::console($calldata->toJSON(),$ret->toJSON());
        //print_r($calldata);
        //print_r($ret);
        // exit;
        return $ret;
    }
);
// exit;
$products = $source->getProducts(function($prd){
    $newProd = new Product($prd);
    //exit;
    //Log::console($prd->toJSON(),$newProd->toJSON());
});
echo "\n---- script done in ".(time()-$tick)." ---- \n";

/*
��������� �����������
������ ���������� ���������
�����: https://shared-24.smartape.ru
�����: user62630
������: ISR1CRCq8s1H

IP-����� ����� ������: 188.127.230.8

FTP ������
������: shared-24.smartape.ru
������������: user62630
������: ISR1CRCq8s1H

MySQL:
��� �������:  Host: 127.0.0.1  ��� localhost          https://shared-24.smartape.ru/user62630/phpmyadmin/
��� ����: 0163694_default
����� ������������:  0163694_default
������: Y0w3A4s6


���� NS �������:
ns1.smartape.ru.
ns2.smartape.ru.

======================================

����� �����

s62630.smrtp.ru

���� � ����� ������ �������� ��������   http://s62630.smrtp.ru/admin/

�����: admin

������: c2t55r54b9


*/
/*
delete from attribute_group;
delete from attribute_group_description;
delete from attribute_description;
delete from attribute;
delete from manufacturer;
delete from category_to_store;
delete from category_to_layout;
delete from category_description;
delete from category_path;
delete from category;
delete from product_attribute;
delete from product_description;
delete from product_image;
delete from product_option;
delete from product_option_value;
delete from product_related;
delete from product_to_category;
delete from product_to_layout;
delete from product_to_store;
delete from product;
*/
?>
