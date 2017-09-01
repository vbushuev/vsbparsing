<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\Strings;
use core\HTTPConnector as Http;

class Dushevoi{
    protected $url = "https://www.dushevoi.ru";
    protected $html = false;
    protected $categories = false;
    protected $internal_categories = [];
    protected $internal_categoriesById = [];
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
            $id = Strings::transcript($li["id"]);
            $li["id"] = $id;
            $parent_id = isset($li["parent_id"])?Strings::transcript($li["parent_id"]):-1;
            if($parent_id!=-1){
                $p1_id = $this->internal_categories[$parent_id];
                $parent = [$p1_id->category_id];
                // if(isset($this->internal_categoriesById[$p1_id->parent_id]))$parent[] = $p1_id->parent_id;
                if(intval($p1_id->parent_id)!==0)$parent[] = $p1_id->parent_id;
                $parent=array_reverse($parent);
            }else $parent = false;


            $cat = new Category;
            $url = $li["url"];
            // $brands = [];
            // foreach ($li["brands"] as $key) {
            //     $brands[] = urlencode($this->brands[$key]);
            // }
            // $url.="?".join('&',$brands);
            $cat->fromArray(['external_id' => $id,"id"=>$id,"parent_id"=>$parent,'title' => $li["title"], 'url' => $url,'brands'=>isset($li["brands"])?$li["brands"]:[]]);
            $this->categories[] = $li;
            if(!isset($this->internal_categories[$id]) || !is_null($this->internal_categories[$id])){
                $opcat =  (!is_null($_callback))?$_callback($cat):null;
                $this->internal_categories[$id] = $opcat;
                // $this->internal_categoriesById[$opcat->category_id] = $opcat;
                // echo $id."(".$opcat->category_id.") -> ".$parent_id."(".(is_array($parent)?join($parent,"->"):$parent).")\n";
            }


        }
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        // $lastcat = (file_exists("tdlcp".date("YmdH").".json"))?json_decode(file_get_contents("tdlcp".date("YmdH").".json")):null;
        $products = [];
        if($this->categories===false)return $products;
        // $http= new Http(["tor"=>true]);

        foreach ($this->categories as $cat) {
            $cat_id = $cat["id"];
            if($cat["url"]===false)continue;
            // if(!is_null($lastcat) && $cat_id!=$lastcat)continue;
            $lastcat = $cat;
            $url = $cat["url"];
            $http= new Http();
            $page = $http->fetch($url,"GET",[]);
            $doc = \phpQuery::newDocument($page);
            echo "Getting {$url}\n";
            $prods = $doc[".goods .good-item > div > div:nth-child(1) > a"];
            foreach ($prods as $prod) {
                $prd = $this->productsFromPage($this->url.pq($prod)->attr("href"),$cat_id);
                if(!is_null($_callback))$_callback($prd);
            }

            $lastPage = 0;
            foreach($doc[".pagination a"] as $pageUrl){
                $val = intval(pq($pageUrl)->text());
                if($val>1)$lastPage = $val;
            }
            Log::console("LAST PAGE: {$lastPage}");
            \phpQuery::unloadDocuments();
            for($i=2;$i<=$lastPage;++$i){
                $url_p=$url."/?page={$i}";
                $page = $http->fetch($url_p,"GET",[]);
                $doc = \phpQuery::newDocument($page);
                echo "Getting {$url_p}\n";
                $prods = $doc[".goods .good-item > div > div:nth-child(1) > a"];
                foreach ($prods as $prod) {
                    $prd = $this->productsFromPage($this->url.pq($prod)->attr("href"),$cat_id);
                    if(!is_null($_callback) && !is_null($prd) )$_callback($prd);
                    //break;
                }
                \phpQuery::unloadDocuments();
            }
            // \phpQuery::unloadDocuments();
            $http->close();
            // file_put_contents("tdlcp".date("YmdH").".json",json_encode($cat_id));
        }

        return $products;
    }
    protected function productsFromPage($prodUrl,$cat_id){
        $http= new Http();
        Log::console ("Fetching product [{$cat_id}]".$prodUrl);
        $productPage = $http->fetch($prodUrl,"GET",[]);
        file_put_contents("ppage.html",$productPage);
        $pdoc = \phpQuery::newDocument($productPage);
        $pp = pq($pdoc[".container"]);
        $pictures = [
            "http:".$pp->find(".main-photo img")->attr("src")
        ];
        $related = [];
        $params=[];
        $description = '<table class="product_description">';
        $i=1;

        $brand = $pp->find("#attrs [itemprop=brand] [itemprop=name]")->text();
        $vendor = $pp->find("#attrs [itemprop=additionalProperty]:first [itemprop=value]")->text();
        $pics = $pdoc[".well.thumbs .thumbnail .trans"];
        foreach ($pics as $pic) $pictures[]="http:".preg_replace('/_thumb/im','',pq($pic)->attr("src"));


        foreach($pdoc["#complectations tbody tr"] as $composit){
            $plink = trim(pq($composit)->find("td:nth-child(1) > a")->attr("href"));
            $name = trim(pq($composit)->find("td:nth-child(1) > a")->text());
            $price = preg_replace('/&nbsp;|\s/im','',pq($composit)->find("td:nth-child(2) > .price")->text());
            if(!empty($plink) && !empty($price)) {
                $description.='<tr><th width="70%">'.$name.'</th><th>'.$price.'</th></tr>';
                // $related[] = $plink;continue;
                $relprd = $this->productsFromPage($this->url.$plink,"");
                $related[] = $relprd;

            }

        }
        $description.='</table>';

        $category_id = null;
        if(isset($this->internal_categories[$cat_id])){
            $internal_category = $this->internal_categories[$cat_id];
            if(isset($internal_category->category_id))$category_id = $internal_category->category_id;
        }

        // if(isset($this->categories[$cat_id]) && isset($this->categories[$cat_id]->brands)){
        //     if(count($this->categories[$cat_id]->brands) && !in_array($vendor,$this->categories[$cat_id]->brands))return null;
        // }
        $props = $pdoc["#attrs table tr[itemprop=additionalProperty]:gt(1)"];
        foreach($props as $prop){
            $name = pq($prop)->find(".attrname")->text();
            $value = pq($prop)->find("[itemprop=value]")->text();
            $params[$name]=$value;
        }
        $prd = new Product;
        $products[] = $prd->fromArray([
            //"id"=>,
            "title" => $pp->find("h1[itemprop=name]")->text(),
            "external_id"=>$pp->find("#root-id")->val(),
            "url"=>$prodUrl,
            "brand"=>$brand,
            "sku"=>$pp->find("[itemprop=productID]")->text(),
            "vendor"=>$vendor,
            "description" =>$description,//$pp->find(".props_group ")->html(),
            "category_id" => $category_id,
            "images"=>$pictures,
            "related"=>$related,
            "price"=>preg_replace("/\s*|&nbsp;/im","",$pp->find("[itemprop=price]")->text()),
            "currency"=>"RUB",
            "params"=>["Характеристики"=>$params],
            //"quantity"=>$quantity
        ]);

        //print_r($prd->toArray());exit;
        $http->close();
        return $prd;
    }

};
?>
