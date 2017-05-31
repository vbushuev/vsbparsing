<?php
namespace adaptor\yml;
use core\objects\Category as Category;
use core\objects\Product as Product;
class Reader{
    protected $syml = null;
    public function __construct($yml){
        $this->syml = simplexml_load_string($yml);
    }
    public function getCurrencies(){
        $curs = [];
        if(!isset($this->syml->shop->currencies->currency))return $curs;

        foreach($this->syml->shop->currencies->currency as $currency){
            //<currency id='UAH' rate='1.00000000'/>
            $curs[strval($currency["id"])]=floatval($currency["rate"]);
        }
        return $curs;
    }
    public function getCategories(){
        //создаем массив категорий
        $c = [];
        foreach ($this->syml->shop->categories->category as $value) {
            $parent = ($value['parentId'])?strval($value['parentId']):'0';
            $id = strval($value['id']);
            $tit = strval($value['0']);
        	$cat = new Category;

        	$c[$id] = $cat->fromArray(['external_id' => $id,'title' => $tit, 'parent_id' => $parent]);
        }
        //Сортируем массив, чтоб сначала шли основные категории (без родителя)
        //usort($c, function($a, $b){return ($a->parent_id - $b->parent_id);});
        return $c;
    }
    public function getProducts($cur=false){
        $products = [];
        //Создаем массив с товарами
        foreach ($this->syml->shop->offers->offer as $offer) {
        	$product_id = strval($offer['id']);
        	$url = strval($offer->url);
        	$oldprice = strval($offer->oldprice);
            $currency = strval($offer->currencyId);
        	$price = strval($offer->price);
            if($cur!==false){
                $cc = $this->getCurrencies();
                if(isset($cc[$cur])){
                    $price = $price*$cc[$cur];
                    $currency = $cur;
                }
            }
        	//$currency = strval($offer->currencyId);
        	//$old_category = strval($offer->categoryId);
        	$name = strval($offer->name);
        	$description = strval($offer->description['0']);
        	foreach ($offer->picture as $picture)$pictures[] = strval($picture);
            $params=[];
            foreach ($offer->param as $p) {
                $params[]=['name' => strval($p['name']), 'value' => strval($p)];
            }
        	// $param[] = ['name' => strval($offer->param['0']['name']), 'value' => strval($offer->param['0'])];
        	// $param[] = ['name' => strval($offer->param['1']['name']), 'value' => strval($offer->param['1'])];
        	$quantity = intval($offer->quantity);
            $prd = new Product;
        	$products[] = $prd->fromArray([
                "id"=>"",
                "external_id"=>$product_id,
                "url"=>$url,
                "title" => $name,
                "brand"=>"",
                "sku"=>strval($offer->vendorCode),
                "vendor"=>strval($offer->vendor),
                "description" =>$description,
                "category_id" => strval($offer->categoryId),
                "images"=>$pictures,
                "price"=>$price,
                "currency"=>$currency,
                "params"=>$params,
                "quantity"=>$quantity
            ]);
        	unset($param);
        	unset($pictures);
        }
        return $products;
    }
};
?>
