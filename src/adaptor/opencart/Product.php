<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use core\objects\Product as coreProduct;
class Product extends Table{
    protected $fillable = ["model","sku","upc","ean","jan","isbn","mpn","location","quantity","stock_status_id","image","manufacturer_id","shipping","price","points","tax_class_id","date_available","weight","weight_class_id","length","width","height","length_class_id","subtract","minimum","sort_order","status","viewed"];
    protected $_cfg;
    public function __construct(coreProduct $prd = null){
        parent::__construct('product','product_id','date_added',"date_modified");
        $this->_cfg = Config::opencart();
        if($prd!=null){
            $loadedRelated = [];
            foreach($prd->related as $related){
                $loadedRelated[] = new Product($related);
            }
            $manufacturer = new Manufacturer(["name"=>$prd->vendor,"image"=>""]);
            $new_data=[
                "model"=>$prd->brand,
                "sku"=>$prd->sku,
                "upc"=>"",
                "ean"=>"",
                "jan"=>"",
                "isbn"=>"",
                "mpn"=>"",
                "location"=>"",
                "quantity"=>"",
                "stock_status_id"=>"7",
                "image"=>"",
                "manufacturer_id"=>$manufacturer->manufacturer_id,
                "shipping"=>"0",
                "price"=>$prd->price,
                "points"=>"0",
                "tax_class_id"=>"9",
                "date_available"=>date("Y-m-d H:i:s"),
                "weight"=>"0",
                "weight_class_id"=>"1",
                "length"=>"0",
                "width"=>"0",
                "height"=>"0",
                "length_class_id"=>"2",
                "subtract"=>"0",
                "minimum"=>"1",
                "sort_order"=>"0",
                "status"=>"1",
                "viewed"=>"0"
            ];
            try{
                $this->find(["sku"=>$prd->sku]);
                Log::debug( "Product #".$this->product_id." updated.");
                $this->save();
            }
            catch(\Exception $e){
                $this->create($new_data);
                Log::debug( "Product #".$this->product_id." created.");
            }
            //Description
            $prd->id = $this->product_id;
            new ProductDescription($prd);
            if(!is_null($prd->category_id))new ProductCategory($prd);
            new ProductStore($prd);
            $this->checkImages($prd);
            $this->checkProperties($prd);
            foreach ($loadedRelated as $relprd) {
                new ProductRelated([
                    "product_id"=>$this->product_id,
                    "related_id"=>$relprd->product_id
                ]);
            }
        }
    }
    public function checkProperties(coreProduct $prd){
        foreach($prd->params as $group=>$params){
            $attrgroup = new AttributeGroupDescription(["name"=>$group]);
            foreach ($params as $key => $value) {
                try{
                    $attr = new AttributeDescription(["name"=>$key,"attribute_group_id"=>$attrgroup->attribute_group_id]);
                    new ProductAttribute(["product_id"=>$this->product_id,"attribute_id"=>$attr->attribute_id,"text"=>$value]);
                }
                catch(\Exception $e){
                    Log::debug("attribute {$key} is NOT REGISTERED.".$e->getTraceAsString());
                }
            }
        }
    }
    public function checkImages(coreProduct $prd){
        $urls=[];
        $ordering = 1;
        $tut = $this;
        $i=0;
        foreach ($prd->images as $img) {
            $pu = parse_url($img);
            $pi = pathinfo($pu["path"]);
            $filepath = $this->_cfg["images"]["path"];
            $this->checkDirectory($filepath);
            $filename = $tut->product_id."_".$i."_".$pi["basename"];
            $filepath.=$filename;
            $guid = $this->_cfg["images"]["cms_path"];
            if(!file_exists($filepath))$urls[$img]=["url"=>$img,"method"=>"GET","data"=>[],"i"=>$i,"id"=>$tut->product_id];
            $i++;
        }
        if(count($urls)){
            $http = new HTTP;
            $http->multiFetch($urls,function($calldata)use($tut){
                if(mb_strlen($calldata["response"])>0){
                    $pu = parse_url($calldata["url"]);
                    $pi = pathinfo($pu["path"]);
                    print_r($calldata);
                    $filename = $tut->product_id."_".$calldata["request"]["i"]."_".$pi["basename"];
                    $filepath = $this->_cfg["images"]["path"];
                    $filepath.=$filename;
                    file_put_contents($filepath,$calldata["response"]);
                    $image = new ProductImage(["image"=>$tut->_cfg["images"]["cms_path"].$filename,"product_id"=>$tut->product_id]);
                    if($calldata["request"]["i"]==0)$tut->update(["image"=>$tut->_cfg["images"]["cms_path"].$filename]);
                    Log::debug( "loaded image ".$calldata["url"]." to ".$filepath);
                }
            });
        }
    }
    protected function checkDirectory($dir){
        if(!file_exists($dir)||!is_dir($dir))mkdir($dir,0777,true);
    }
    protected function resize_image($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $pi = pathinfo($file);
        $src = ($pi["extension"]=="png")?imagecreatefrompng($file):imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
};
?>
