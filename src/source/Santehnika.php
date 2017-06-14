<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\HTTPConnector as Http;

class Santehnika{
    protected $url = "http://santehnika-online.ru";
    protected $html = false;
    protected $categories = false;
    protected $internal_categories = [];
    protected $brands=[];
    protected $_cfg=[];
    public function __construct(){
        $this->html = new Http();

        $this->brands = json_decode(file_get_contents('brands.json'),true);
        //Log::console($this->brands);
    }
    public function __destructor(){
        $this->html->close();
    }
    public function getCategories($cats=[],callable $_callback=null){
        $this->categories = [];
        $this->internal_categories = [];
        foreach ($cats as $li) {
            $id = $li["url"];
            $cat = new Category;
            $url = $li["url"];
            // $brands = [];
            // foreach ($li["brands"] as $key) {
            //     $brands[] = urlencode($this->brands[$key]);
            // }
            // $url.="?".join('&',$brands);
            $cat->fromArray(['external_id' => $li["id"],'title' => $li["title"], 'url' => $url,'brands'=>$li["brands"]]);
            $this->categories[$id] = $cat;
            $this->internal_categories[$id] = (!is_null($_callback))?$_callback($cat):null;
        }
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        $products = [];
        if($this->categories===false)return $products;
        $http= new Http();
        foreach ($this->categories as $cat_id => $cat) {
            $url = $cat->url;

            $page = $http->fetch($url,"GET",[]);
            $doc = \phpQuery::newDocument($page);
            echo "Getting {$url}\n";
            $prods = $doc[".product .photo_link"];
            foreach ($prods as $prod) {
                $prd = $this->productsFromPage($this->url.pq($prod)->attr("href"),$cat_id);
                if(!is_null($_callback))$_callback($prd);
            }

            $lastPage = 0;
            foreach($doc[".paginator a"] as $pageUrl){
                $val = intval(pq($pageUrl)->text());
                if($val>1)$lastPage = $val;
            }
            Log::console("LAST PAGE: {$lastPage}");
            \phpQuery::unloadDocuments();
            for($i=2;$i<=$lastPage;++$i){
                $url_p=$url."&PAGEN_1={$i}";
                $page = $http->fetch($url_p,"GET",[]);
                $doc = \phpQuery::newDocument($page);
                echo "Getting {$url_p}\n";
                $prods = $doc[".product .photo_link"];
                foreach ($prods as $prod) {
                    $prd = $this->productsFromPage($this->url.pq($prod)->attr("href"),$cat_id);
                    if(!is_null($_callback) && !is_null($prd) )$_callback($prd);
                    //break;
                }
            }
            \phpQuery::unloadDocuments();
            //break;
        }
        $http->close();
        return $products;
    }
    protected function productsFromPage($prodUrl,$cat_id){
        $http= new Http();
        Log::console ("Fetching product ".$prodUrl);
        $productPage = $http->fetch($prodUrl,"GET",[]);
        file_put_contents("ppage.html",$productPage);
        $pdoc = \phpQuery::newDocument($productPage);
        $pictures = [];
        $pics = $pdoc[".preview ul.prew li a img"];
        foreach ($pics as $pic) {
            $pictures[]=$this->url.pq($pic)->attr("src");
        }
        $related = [];
        //print_r($composits);exit;
        $i=1;
        foreach($pdoc[".icon-galka-complgreen.cmplproduct .cmplopis .cmplopislink"] as $composit){

            $plink = trim(pq($composit)->attr("href"));
            //Log::console("Composite product hrel ".$this->url.$plink);
            if(!empty($plink)) {
                $relprd = $this->productsFromPage($this->url.$plink,"");
                $related[] = $relprd;
            }
            // print_r($relprd->toArray());
            // exit;
        }
        //"#cmplproduct1817773 > div:nth-child(3) > a:nth-child(1)"

        $pp = pq($pdoc[".cartproduct"]);

        $category_id = null;
        if(isset($this->internal_categories[$cat_id])){
            $internal_category = $this->internal_categories[$cat_id];
            if(isset($internal_category->category_id))$category_id = $internal_category->category_id;
        }
        $vendor = $pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(2) > span.property_value > a")->text();
        if(isset($this->categories[$cat_id]) && isset($this->categories[$cat_id]->brands)){
            if(count($this->categories[$cat_id]->brands) && !in_array($vendor,$this->categories[$cat_id]->brands))return null;
        }
        $props = $pdoc[".props_group"];
        $params=[];
        foreach($props as $prop){
            $title = pq($prop)->find(".title")->text();
            $title = (empty($title))?"Характеристики":$title;
            $params[$title]=[];
            //Log::console("prop title ".$title);
            foreach(pq($prop)->find("ul li:not(.hide)") as $param) {
                $name = trim(pq($param)->find(".name")->text());
                $value = trim(pq($param)->find(".value")->text());
                //Log::console("prop ".$name."=".$value);
                $params[$title][$name]=$value;
            }
        }
        $prd = new Product;
        $products[] = $prd->fromArray([
            //"id"=>,
            "title" => $pp->find(".zagl h1")->text(),
            "external_id"=>$pp->find("#root-id")->val(),
            "url"=>$prodUrl,
            "brand"=>$pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(4) > span.property_value")->text(),
            "sku"=>$pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(1) > span.property_value > noindex")->text(),
            "vendor"=>$vendor,
            "description" =>$pp->find(".props_group ")->html(),
            "category_id" => $category_id,
            "images"=>$pictures,
            "related"=>$related,
            "price"=>preg_replace("/\s*/im","",$pp->find(".price .newprice")->attr("data-price")),
            "currency"=>"RUB",
            "params"=>$params,
            //"quantity"=>$quantity
        ]);
        $http->close();
        return $prd;
    }

};
?>
