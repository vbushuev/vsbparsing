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
            $this->categories[$id] = $li;
            $this->internal_categories[$id] = (!is_null($_callback))?$_callback($cat):null;
        }
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        $lastcat = (file_exists("tdlcp".date("YmdH")."json"))?json_decode(file_get_contents("tdlcp".date("YmdH")."json")):null;
        $products = [];
        if($this->categories===false)return $products;
        $http= new Http();
        foreach ($this->categories as $cat_id => $cat) {
            if(!is_null($lastcat) && $cat_id!=$lastcat)continue;
            $lastcat = $cat;
            $url = $cat["url"]."?perpage=108";
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
            $http->close();
            file_put_contents("tdlcp".date("YmdH")."json",json_encode($cat_it));
        }

        return $products;
    }
    protected function productsFromPage($prodUrl,$cat_id){
        $http= new Http();
        Log::console ("Fetching product ".$prodUrl);
        $productPage = $http->fetch($prodUrl,"GET",[]);
        file_put_contents("ppage.html",$productPage);
        $pdoc = \phpQuery::newDocument($productPage);
        $pp = pq($pdoc[".cartproduct"]);
        $pictures = [];
        $related = [];
        $params=[];
        $description = '<table class="product_description">';
        $i=1;

        $brand = $pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(4) > span.property_value")->text();
        $vendor = $pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(2) > span.property_value > a")->text();
        $alowedBrands = (isset($this->categories[$cat_id]) && isset($this->categories[$cat_id]["brands"]) && count($this->categories[$cat_id]["brands"]))?$this->categories[$cat_id]["brands"]:[$vendor];
        if(!in_array($vendor,$alowedBrands)){
            Log::console("Wrond brand: ".$vendor);
            return null;
        }
        // $pics = $pdoc[".preview ul.prew li a img"];
        $pics = $pdoc[".preview ul.prew .product_fill_img.priview_images"];
        foreach ($pics as $pic) $pictures[]=$this->url.pq($pic)->attr("href");


        foreach($pdoc[".icon-galka-complgreen.cmplproduct"] as $composit){
            $plink = trim(pq($composit)->find(".cmplopislink")->attr("href"));
            $name = trim(pq($composit)->find(".cmplopislink")->text());
            $price = trim(pq($composit)->find(".cmplprice")->html());
            $containsStr = trim(pq($composit)->find(".cmplopis1_detali")->text());
            $contains=preg_split("/шт\./ui",$containsStr);
            array_splice($contains, count($contains)-1);

            $description.='<tr><th width="70%">'.$name.'</th><th>'.$price.'</th></tr>';
            foreach ($contains as $conttext) $description.='<tr><td colspan="2">'.$conttext.'шт.</td></tr>';
            $description.='<tr><td colspan="2">Инструкция 1 шт.</td></tr>';
            if(!empty($plink)) {
                $relprd = $this->productsFromPage($this->url.$plink,"");
                $related[] = $relprd;
            }

        }
        $description.='</table>';
        // foreach($pdoc[".icon-galka-complgreen.cmplproduct .cmplopis .cmplopislink"] as $composit){
        //     $plink = trim(pq($composit)->attr("href"));
        //     if(!empty($plink)) {
        //         $relprd = $this->productsFromPage($this->url.$plink,"");
        //         $related[] = $relprd;
        //     }
        // }



        $category_id = null;
        if(isset($this->internal_categories[$cat_id])){
            $internal_category = $this->internal_categories[$cat_id];
            if(isset($internal_category->category_id))$category_id = $internal_category->category_id;
        }

        if(isset($this->categories[$cat_id]) && isset($this->categories[$cat_id]->brands)){
            if(count($this->categories[$cat_id]->brands) && !in_array($vendor,$this->categories[$cat_id]->brands))return null;
        }
        $props = $pdoc[".props_group"];

        foreach($props as $prop){
            $title = pq($prop)->find(".title")->text();
            $title = (empty($title))?"Основные":$title;
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
            "brand"=>$brand,
            "sku"=>$pp->find("div.rightcol > div.wrap > div.leftsubcol > ul > li:nth-child(1) > span.property_value > noindex")->text(),
            "vendor"=>$vendor,
            "description" =>$description,//$pp->find(".props_group ")->html(),
            "category_id" => $category_id,
            "images"=>$pictures,
            "related"=>$related,
            "price"=>preg_replace("/\s*/im","",$pp->find(".price .newprice")->attr("data-price")),
            "currency"=>"RUB",
            "params"=>$params,
            //"quantity"=>$quantity
        ]);
        //print_r($prd->toArray());exit;
        $http->close();
        return $prd;
    }

};
?>
