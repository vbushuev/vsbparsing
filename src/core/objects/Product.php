<?php
namespace core\objects;
use core\Common as Common;
class Product extends Common{
    protected $publicData = [
        "id"=>"",
        "external_id"=>"",
        "url"=>"",
        "title" => "",
        "brand"=>"",
        "sku"=>"",
        "vendor"=>"",
        "description" =>"",
        "category_id" => "",
        "images"=>[],
        "price"=>0.0,
        "currency"=>"",
        "params"=>[],
        "quantity"=>0
    ];
};
?>
