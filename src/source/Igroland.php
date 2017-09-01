<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\HTTPConnector as Http;

class Igroland{

    protected $categories = false;
    protected $internal_categories = [];
    protected $brands=[];
    protected $_cfg=[];
    public function __construct(){}
    public function __destructor(){}
    public function getCategories($cats=[],callable $_callback=null){
        $this->categories = [];
        foreach ($cats as $li) {
            $id = $li["category"];
            $cat = isset($this->categories[$id])?$this->categories[$id]:[
                'external_id' => $li["category"],
                'title' => $li["category"],
                'url' => '',
                'brands'=>[],
                'goods'=>[]
            ];

            $category = new Category;
            $category->fromArray($cat);

            $icat = (!is_null($_callback))?$_callback($category):null;

            $cat["internalCategory"] = $icat;
            $cat["goods"][]=$li;
            $this->categories[$id] = $cat;
        }
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        if($this->categories===false)return $products;
        foreach ($this->categories as $cat_id => $cat) {
            $category_id = $cat["internalCategory"];
            foreach($cat["goods"] as $good){
                $prd = new Product;
                $prd->fromArray([
                    //"id"=>,
                    "title" => $good["title"],
                    "external_id"=>$good["sku"],
                    "url"=>"",
                    "brand"=>"",
                    "sku"=>$good["sku"],
                    "vendor"=>"",
                    "description" =>"",
                    "category_id" => $category_id->category_id,
                    "images"=>[],
                    "related"=>[],
                    "price"=>$good["price"],
                    "currency"=>"RUB",
                    "params"=>[],
                    "quantity"=>$good["quantity"]
                ]);
                if(!is_null($_callback) && !is_null($prd) )$_callback($prd);
            }
        }
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
