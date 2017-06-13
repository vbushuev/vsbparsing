<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\HTTPConnector as Http;

class Ebay{
    protected $url = "http://stores.ebay.com";
    protected $html = false;
    protected $categories = false;
    public function __construct($catalog="T-shirt-Hoarders"){
        $this->url.='/'.$catalog;
        $this->html = new Http();
    }
    public function __destructor(){
        $this->html->close();
    }
    public function getCategories(){
        $this->categories = [];
        $this->html = new Http();
        $data = $this->html->fetch($this->url,"GET",[]);
        $doc = \phpQuery::newDocument($data);
        $list = $doc["ul.lev1 > li > a"];
        $i=0;
        foreach ($list as $li) {
            $id = pq($li)->text();
            $cat = new Category;
            $this->categories[$id] = $cat->fromArray(['external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")]);
        }
        $this->html->close();
        \phpQuery::unloadDocuments();
        return $this->categories;
    }
    public function getProducts(){
        $products = [];
        if($this->categories===false)$this->getCategories();
        $http= new Http();
        foreach ($this->categories as $cat_id => $cat) {
            $url = $cat->url;
            $page = $http->fetch($url,"GET",[]);
            $doc = \phpQuery::newDocument($page);
            echo "Getting {$url}\n";
            $prods = $doc[".gallery[itemscope=itemscope] .details .ttl.g-std a"];
            foreach ($prods as $prod) {
                $products = array_merge($products,$this->productsFromPage($prod,$cat_id));
            }
            $pages = $doc[".pages a"];

            foreach ($pages as $pageUrl) {
                if(pq($pageUrl)->attr("style")!="display:none"){
                    $url = $this->url.pq($pageUrl)->attr("href");
                    //echo pq($pageUrl)->attr("href")."  ".pq($pageUrl)->attr("style")."\n";
                    $page = $http->fetch($url,"GET",[]);
                    $doc = \phpQuery::newDocument($page);
                    echo "Getting {$url}\n";
                    $prods = $doc[".gallery[itemscope=itemscope] .details .ttl.g-std a"];
                    foreach ($prods as $prod) {
                        $products = array_merge($products,$this->productsFromPage($prod,$cat_id));
                    }
                }
            }
            \phpQuery::unloadDocuments();
            //break;
        }
        $http->close();
        return $products;
    }
    protected function productsFromPage($prod,$cat_id){
        $products = [];
        $http= new Http();
        $pp = pq($prod);
        $productPage = $http->fetch($pp->attr("href"),"GET",[]);
        $pdoc = \phpQuery::newDocument($productPage);
        $sizes = $pdoc[".msku-sel option"];
        $params=[];
        foreach ($sizes as $size){
            $txt = pq($size)->text();
            if(preg_match("/[2-5smlx]+/i",$txt))$params["size"][]=preg_replace("/.*([2-5smlx]+).*/i","$1",$txt);
        }
        $images = $pdoc["#vi_main_img_fs .img .tdThumb img"];
        $pictures = [];
        foreach ($images as $img) {
            if(preg_match("/s\-l64\.jpg/",pq($img)->attr("src"))){
                $pictures[]=preg_replace("/s\-l64\.jpg/","s-l500.jpg",pq($img)->attr("src"));
            }
        }
        $price = preg_replace("/\D*(\d+)(,|\.)(\d+)\D*/i","$1.$3",$pdoc["#prcIsum"]->text());
        $quantity = preg_replace("/\s*(\d+)[\s\S]*/im","$1",$pdoc["#qtySubTxt"]->text());
        $prd = new Product;
        $products[] = $prd->fromArray([
            //"id"=>,
            "external_id"=>$pp->attr("id"),
            "url"=>$pp->attr("href"),
            "title" => $pp->text(),
            //"brand"=>"",
            "sku"=>$pp->attr("id"),
            //"vendor"=>strval($offer->vendor),
            //"description" =>$description,
            "category_id" => $cat_id,
            "images"=>$pictures,
            "price"=>$price,
            "currency"=>"USD",
            "params"=>$params,
            "quantity"=>$quantity
        ]);
        $http->close();
        return $products;
    }

};
?>
