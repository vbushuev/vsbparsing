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
        if(file_exists("categories-".date("Y-m-d").".json")){
            $this->categories = json_decode(file_get_contents("categories-".date("Y-m-d").".json"),true);
        }
    }
    public function __destructor(){
        $this->html->close();
    }
    public function getCategories(callable $_callback=null){
        $this->categories = [];
        $this->html = new Http();
        $data = $this->html->fetch($this->url,"GET",[]);
        $doc = \phpQuery::newDocument($data);
        $list = $doc["ul.lev1 > li > a"];
        $i=0;
        foreach ($list as $li) {
            $id = pq($li)->text();
            $cat = new Category;
            // $this->categories[$id] = $cat->fromArray(['external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")]);
            // $this->categories[$id] = ["viewed"=>0,"data"=>$cat->fromArray(['external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")])];
            $this->categories[$id] = ["viewed"=>0,'external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")];
        }
        $this->html->close();
        Log::console($this->categories);
        \phpQuery::unloadDocuments();
        file_put_contents("categories-".date("Y-m-d").".json",json_encode($this->categories,JSON_PRETTY_PRINT));
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        //$products = [];
        Log::console("callback = ".(is_null($_callback)?"not func":"func"));
        if($this->categories===false)$this->getCategories();
        $http= new Http();
        $cat = new Category;
        foreach ($this->categories as $cat_id => $catArr) {
            if($this->categories[$cat_id]["viewed"]=="0"){
                $cat->fromArray($catArr);
                $url = $cat->url;
                $page = $http->fetch($url,"GET",[]);
                $doc = \phpQuery::newDocument($page);
                //echo "Getting {$url}\n";
                $prods = $doc[".gallery[itemscope=itemscope] .details .ttl.g-std a"];
                foreach ($prods as $prod) {
                    $prd = $this->productsFromPage($prod,$cat_id);
                    if(!is_null($_callback))$_callback($prd);
                }
                $pages = $doc[".pages a"];
                foreach ($pages as $pageUrl) {
                    if(pq($pageUrl)->attr("style")!="display:none"){
                        $url = $this->url.pq($pageUrl)->attr("href");
                        echo "Getting page: ".$url."  {".pq($pageUrl)->attr("style")."}\n";
                        $page = $http->fetch($url,"GET",[]);
                        $doc = \phpQuery::newDocument($page);
                        //echo "Getting {$url}\n";
                        $prods = $doc[".gallery[itemscope=itemscope] .details .ttl.g-std a"];
                        foreach ($prods as $prod) {
                            $prd = $this->productsFromPage($prod,$cat_id);
                            if(!is_null($_callback))$_callback($prd);
                        }
                    }
                }
                \phpQuery::unloadDocuments();
                $this->categories[$cat_id]["viewed"] = "1";
                file_put_contents("categories-".date("Y-m-d").".json",json_encode($this->categories,JSON_PRETTY_PRINT));
                //break;
            }
        }
        $http->close();
        return $products;
    }
    protected function productsFromPage($prod,$cat_id){
        $http= new Http();
        $pp = pq($prod);
        Log::console("Getting product [".$pp->attr("href")."]");
        $productPage = $http->fetch($pp->attr("href"),"GET",[]);
        $pdoc = \phpQuery::newDocument($productPage);
        $sizes = $pdoc[".msku-sel option"];
        $params=[];
        foreach ($sizes as $size){
            $txt = pq($size)->text();
            if(preg_match("/[2-5smlx]+/i",$txt))if(preg_match("/^([2-5smlx]+).*/i",$txt))$params["size"][]=$txt;
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
        $title = preg_replace('/\$\d+[\.,]?\d*/m',"",$pp->text());
        $prd = new Product;
        $prd->fromArray([
            //"id"=>,
            "external_id"=>$pp->attr("id"),
            "url"=>$pp->attr("href"),
            "title" => $title,
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
        return $prd;
    }

};
?>
