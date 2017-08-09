<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\HTTPConnector as Http;

class Smartshoes{
    protected $url = "http://smart-shoes.ru";
    protected $html = false;
    protected $categories = false;
    protected $internal_categories = [];
    protected $_cfg=[];
    protected $watchedPages = [];
    protected $watchedPagesFile = "";
    public function __construct(){
        $this->watchedPagesFile = "store/pages-".date("Y-m-d").".json";
        if(file_exists($this->watchedPagesFile))$this->watchedPages = json_decode(file_get_contents($this->watchedPagesFile),true);
        $this->html = new Http();;
    }
    public function __destructor(){
        $this->html->close();
    }
    public function getCategories($pcats=[],callable $_callback=null){
        $this->categories = [];
        $this->internal_categories = [];
        $pcat = new Category;
        $cat = new Category;
        foreach ($pcats as $key => $cats) {
            // $pcat->fromArray(['external_id' => $li["id"],'title' => $li["title"]]);
            $pcat->fromArray(['external_id' => $key,'title' => $key]);
            $parentcat = (!is_null($_callback))?$_callback($pcat):null;
            foreach ($cats as $li) {
                $cat->fromArray(['external_id' => $li["id"],'title' => $li["title"], 'url' => $li["url"],'brands'=>$li["brands"],'parent_id'=>$parentcat->category_id]);
                // print_r($cat->toArray());exit;
                $this->categories[$li["url"]] = $li;
                $this->internal_categories[$li["url"]] = (!is_null($_callback))?$_callback($cat):null;

            }
        }
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        // $lastcat = (file_exists("tdlcp".date("YmdH")."json"))?json_decode(file_get_contents("tdlcp".date("YmdH")."json")):null;
        $products = [];
        if($this->categories===false)return $products;
        $http= new Http(["tor"=>true]);
        foreach ($this->categories as $cat_id => $cat) {

            $url = $cat["url"]."?SHOWALL_1=1";
            $page = $http->fetch($url,"GET",[]);
            $doc = \phpQuery::newDocument($page);
            echo "Getting {$url}\n";
            $prods = $doc[".bx_catalog_item_images"];
            foreach ($prods as $prod) {
                $productPageUrl = $this->url.pq($prod)->attr("href");
                if(!in_array($productPageUrl,$this->watchedPages)){
                    $prd = $this->productsFromPage($this->url.pq($prod)->attr("href"),$cat_id);
                    if(!is_null($_callback)){
                        $_callback($prd);
                        $this->watchedPages[]=$productPageUrl;
                        $this->storeWatchedPages();
                    }
                }
            }
            \phpQuery::unloadDocuments();
            $http->close();
            // file_put_contents("tdlcp".date("YmdH")."json",json_encode($cat_id));
        }

        return $products;
    }
    protected function productsFromPage($prodUrl,$cat_id){
        $http= new Http();
        Log::console ("Fetching product ".$prodUrl);
        $productPage = $http->fetch($prodUrl,"GET",[]);
        file_put_contents('store/smartshow.lastproduct.html',$productPage);
        $pdoc = \phpQuery::newDocument($productPage);
        $pp = pq($pdoc[".bx_item_container"]);
        $pictures = [];
        $params=[];
        $description = '<table class="product_description">';
        $i=1;

        $title = preg_replace("/'/im","\'",trim($pp->find(".bx_item_title")->text()));

        $brand = "";//$pp->find(".bx_item_detail_inc_one_container")->html();
        $vendor = preg_replace('/Производитель:/iu','',$pp->find("div.item_price > p:nth-child(8)")->text());

        $pics = $pp->find(".bx_slide .cnt_item");
        foreach ($pics as $pic) $pictures[]=$this->url.pq($pic)->attr("data-src");

        $category_id = null;
        if(isset($this->internal_categories[$cat_id])){
            $internal_category = $this->internal_categories[$cat_id];
            if(isset($internal_category->category_id))$category_id = $internal_category->category_id;
        }

        $props = $pdoc[".bx_size ul li"];
        $params["Размер"]=[];
        foreach($props as $prop){
            $title = "Размер";
            $name = pq($prop)->find(".cnt")->text();
            $value = $name;
            $params[$title][$name]=$value;
        }
        $prd = new Product;
        // $products[] = $prd->fromArray([
        $products[] = $prd->fromArray([
            //"id"=>,
            "title" => $title,
            "external_id"=>$pp->find("#root-id")->val(),
            "url"=>$prodUrl,
            "brand"=>$brand,
            "sku"=>preg_replace('/Артикул:/iu','',$pp->find("div.item_price > p:nth-child(6)")->text()),
            "vendor"=>$vendor,
            "description" =>"",//$pp->find(".props_group ")->html(),
            "category_id" => $category_id,
            "images"=>$pictures,
            // "related"=>$related,
            "price"=>preg_replace("/\D*/im","",$pp->find(".item_current_price")->text()),
            "currency"=>"RUB",
            "params"=>$params,
            //"quantity"=>$quantity
        ]);

        // print_r($prd->toArray());exit;
        $http->close();
        return $prd;
    }
    protected function storeWatchedPages(){
        file_put_contents($this->watchedPagesFile,json_encode($this->watchedPages));
    }
    public function getParsedCount(){
        return $this->_parsed;
    }
};
?>
