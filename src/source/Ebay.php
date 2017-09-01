<?php
namespace source;
use core\objects\Category as Category;
use core\objects\Product as Product;
use core\Log as Log;
use core\HTTPConnector as Http;
use db\Connector as DB;

class Ebay{
    protected $url = "http://stores.ebay.com";
    protected $html = false;
    protected $categories = false;
    protected $watchedPages = [];
    protected $watchedPagesFile = "";
    protected $_parsed = 0;
    protected $_categories = [];
    public function __construct($catalog="T-shirt-Hoarders",$cc = [
        "Affliction","American Baller","American Fighter by Affliction","Archaic by Affliction","Diesel","Hurley",
        "Metal Mulisha","Rebel Saints by Affliction","Rock Revival","Sinful by Affliction","Venum","Xtreme Couture by Affliction"
        ]){
        $this->watchedPagesFile = "store/pages-".date("Y-m-d").".json";
        $this->url.='/'.$catalog;
        $this->html = new Http();
        $this->_categories = $cc;
        if(file_exists($this->watchedPagesFile))$this->watchedPages = json_decode(file_get_contents($this->watchedPagesFile),true);
    }
    public function __destructor(){
        $this->html->close();
    }
    public function getCategories(callable $_callback=null,$categories=[]){
        $this->categories = [];
        $this->html = new Http();
        $data = $this->html->fetch($this->url,"GET",[]);
        $doc = \phpQuery::newDocument($data);
        $list = $doc["ul.lev1 > li > a"];
        $i=0;
        foreach ($list as $li) {

            $id = pq($li)->text();
            echo $id. " in [".join($categories,' ')."]\n";
            if(!in_array($id,$categories))continue;
            $cat = new Category;
            // $this->categories[$id] = $cat->fromArray(['external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")]);
            // $this->categories[$id] = ["viewed"=>0,"data"=>$cat->fromArray(['external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")])];
            $this->categories[$id] = ["viewed"=>0,'external_id' => $id,'title' => $id, 'url' => $this->url.pq($li)->attr("href")];
        }
        $this->html->close();
        //Log::console($this->categories);
        \phpQuery::unloadDocuments();
        //file_put_contents("categories-".date("Y-m-d").".json",json_encode($this->categories,JSON_PRETTY_PRINT));
        return $this->categories;
    }
    public function getProducts(callable $_callback=null){
        $this_url = $this->url;
        if($this->categories===false)$this->getCategories(function(){},$this->_categories);
        $http= new Http();
        foreach ($this->categories as $cat_id => $catArr) {
            $pageUrl = $catArr["url"];
            $ipage = 0;
            do{
                Log::console("category ".$cat_id." page: ".(++$ipage));
                $page = $http->fetch($pageUrl,"GET",[]);
                $doc = \phpQuery::newDocument($page);
                $prods = $doc[".gallery[itemscope=itemscope] .details .ttl.g-std a"];
                $multiProds = [];
                foreach ($prods as $prod) {
                    // $prd = $tut->productsFromPage($prod,$cat_id);
                    // if(!is_null($_callback))$_callback($prd);
                    $productPageUrl = pq($prod)->attr("href");
                    if(!in_array($productPageUrl,$this->watchedPages))
                        $multiProds[$productPageUrl] = ["cat_id"=>$cat_id,"_callback"=>$_callback,"url"=>$productPageUrl,"method"=>"GET","data"=>[]];
                }
                $tut = $this;
                Log::console("Found ".count($multiProds)." new today products");
                if(count($multiProds))$http->multiFetch($multiProds,function($resp)use($tut,$_callback){
                    $prd = $tut->parseProductFromPage($resp["response"],$resp["request"]["cat_id"],$resp["url"]);
                    if(!is_null($_callback))$_callback($prd);
                    else Log::console("callback is null");
                    $tut->watchedPages[]=$resp["url"];
                    $tut->storeWatchedPages();
                    $tut->_parsed++;
                });
                $pageUrlDOM = $doc[".next a.enabled"];
                $pageUrl = (count($pageUrlDOM))?$this->url.pq($pageUrlDOM)->attr("href"):false;
            }while($pageUrl!==false);
            \phpQuery::unloadDocuments();
        }
        $http->close();
        return;
    }
    protected function productsFromPage($prod,$cat_id){
        $http= new Http();
        $pp = pq($prod);
        //Log::console("Getting product [".$pp->attr("href")."]");
        $productPage = $http->fetch($pp->attr("href"),"GET",[]);
        $prd = $this->parseProductFromPage($productPage,$cat_id,$pp->attr("href"));
        $http->close();
        return $prd;
    }
    protected function parseProductFromPage($productPage,$cat_id,$ext_url){
        $pdoc = \phpQuery::newDocument($productPage);
        $sizes = $pdoc[".msku-sel option"];
        $params=[];
        foreach ($sizes as $size){
            $txt = pq($size)->text();
            if(preg_match("/^[2-5smlx]+$/i",$txt))$params["size"][]=$txt;
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
        // $title = preg_replace('/\$\d+[\.,]?\d*/m',"",$pp->text());
        $title = preg_replace('/\$\d+[\.,]?\d*/m',"",$pdoc["h1.it-ttl"]->text());
        $title = preg_replace('/Details about /im','',$title);
        $title = preg_replace('/подробные сведения о /iu','',$title);
        $title = preg_replace('/\-?\s*без перевода\s*/iu','',$title);
        $sku = $pdoc[".clnw-collect.clnw-c.clnw-str"]->attr("data-id"); //clnw-collect clnw-c clnw-str
        $prd = new Product;
        $prd->fromArray([
            //"id"=>,
            "external_id"=>$sku,
            "url"=>$ext_url,
            "title" => $title,
            //"brand"=>"",
            "sku"=>$sku,
            //"vendor"=>strval($offer->vendor),
            //"description" =>$description,
            "category_id" => $cat_id,
            "images"=>$pictures,
            "price"=>$price,
            "currency"=>"USD",
            "params"=>$params,
            "quantity"=>$quantity
        ]);
        //Log::console("Parsed product: ", $prd->toArray());exit;
        return $prd;
    }
    protected function storeWatchedPages(){
        file_put_contents($this->watchedPagesFile,json_encode($this->watchedPages));
    }
    public function getParsedCount(){
        return $this->_parsed;
    }
    public function push(){
        $db = new DB;
        $db->delete("delete from wp_options where option_name like '_transient_wc_var_prices_%'");
        $db->delete("delete from wp_options where option_name like '_transient_timeout_wc_product_children_%'");
        $db->delete("delete from wp_options where option_name like '_transient_wc_product_children_%'");
        $db->delete("delete from wp_options where option_name like '_transient_timeout_wc_var_prices_%'");
        $db->delete("delete from wp_options where option_name like '_transient_timeout_wc_child_has_weight_%'");
        $db->delete("delete from wp_options where option_name like '_transient_wc_child_has_weight_%'");
        $db->delete("delete from wp_options where option_name like '_transient_timeout_wc_child_has_dimensions_%'");
        $db->delete("delete from wp_options where option_name like '_transient_wc_child_has_dimensions_%'");
        $db->delete("delete from wp_options where option_name like '_transient_timeout_wc_related_%'");
        $db->delete("delete from wp_options where option_name like '_transient_wc_related_%'");
    }
};
?>
