<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
use adaptor\joomla\ksenmart\ProductImage as ProductImage;
use adaptor\joomla\ksenmart\ProductCategory as ProductCategory;
use adaptor\joomla\ksenmart\ProductPropertyValues as ProductPropertyValues;
use adaptor\joomla\ksenmart\PropertyValues as PropertyValues;
class Product extends Table{
    protected $fillable = ["parent_id","childs_group","title","alias","price","old_price","purchase_price","price_type","content","introcontent","product_code","in_stock","product_unit","product_packaging","manufacturer","promotion","recommendation","hot","new","published","hits","carted","ordering","metatitle","metadescription","metakeywords","date_added","is_parent","type","tag"];
    protected $_cfg;
    public function __construct(coreProduct $prd = null){
        parent::__construct('ksenmart_products','id','date_added');
        $this->_cfg = Config::ksenmart();
        if($prd!=null){
            $this->publicData=[
                "title"=>$prd->title,
                "alias"=>Strings::transcript($prd->title),
                "price"=>$this->priceAdds($prd->price),
                "old_price"=>"0",
                "purchase_price"=>"0",
                "price_type"=>"1",
                "content"=>$prd->description,
                "introcontent"=>"",
                "product_code"=>$prd->sku,
                "in_stock"=>$prd->quantity,
                "product_unit"=>"0",
                "product_packaging"=>"0.0000",
                "manufacturer"=>$prd->vendor,
                "promotion"=>"0",
                "recommendation"=>"0",
                "hot"=>"0",
                "new"=>"1",
                "published"=>"1",
                "hits"=>"0",
                "carted"=>"0",
                "ordering"=>"0",
                "metatitle"=>"",
                "metadescription"=>"",
                "metakeywords"=>"",
                "is_parent"=>"0",
                "type"=>"product",
                "tag"=>""
            ];
            $found = false;
            try{
                $this->find(['product_code'=>$ctg->sku]);
                $found = true;
            }
            catch(\Exception $e){
                $this->create($this->publicData);
            }
            if($found){
                $this->save();
            }
            $productCategory = new ProductCategory;
            $productCategory->findOrCreate(["product_id"=>$this->id,"category_id"=>$prd->category_id,"is_default"=>"1"]);
            $this->checkImages($prd);
            $this->checkProperties($prd);
            echo "Product #".$this->id."\t".$this->title." loaded.\n";
        }
    }
    public function __set($n,$v){
        if(isset($this->publicData[$n])){
            if($n=="price" && isset($this->_cfg["price"])){
                $v = $this->priceAdds($v);
            }
            $this->publicData[$n] = $v;
        }
    }
    public function priceAdds($p){
        $v = intval($p);
        if($this->_cfg["price"]["type"]=="percent"){
            $v = $v+ $v*$this->_cfg["price"]["adds"]/100;
        }else $v = $v+$this->_cfg["price"]["adds"];
        return $v;
    }
    public function checkProperties(coreProduct $prd){
        $pv = new PropertyValues;
        $ppv = new ProductPropertyValues;
        foreach ($prd->params as $param) {
            if($param["name"]=="Цвет"){
                $pv->findOrCreate(["alias"=>"_".$param["value"],"property_id"=>5,"title"=>$param["value"],"image"=>"","ordering"=>"0"]);
                $ppv->findOrCreate(["product_id"=>$this->id,"property_id"=>"5","value_id"=>$pv->id]);
            }
            else if($param["name"]=="Размер"){
                $pv->findOrCreate(["alias"=>"_".$param["value"],"property_id"=>31,"title"=>$param["value"],"image"=>"","ordering"=>"0"]);
                $ppv->findOrCreate(["product_id"=>$this->id,"property_id"=>"31","value_id"=>$pv->id]);
            }
        }
        /*
        params] => Array
            (
                [0] => Array
                    (
                        [name] => Цвет
                        [value] => Серо-бирюзовый
                    )

                [1] => Array
                    (
                        [name] => Размер
                        [value] => XL
                    )

            )
        */
    }
    public function checkImages(coreProduct $prd){
        $urls=[];
        foreach ($prd->images as $img) {
            $pu = parse_url($img);
            $pi = pathinfo($pu["path"]);
            //echo "check image ".$img." in ".$this->_cfg["images"]["path"].$pi["basename"]."\n";
            if(!file_exists($this->_cfg["images"]["path"]."w30xh30/".$pi["basename"])){
                $urls[$img]=["url"=>$img,"method"=>"GET","data"=>[]];
            }
            $image = new ProductImage;
            try{
                $image->find(["filename"=>" like '%".$pi["basename"]."%'"]);
                if($image->owner_id != $this->id){
                    $image->update(["owner_id"=>$this->id]);
                }
            }
            catch(\Exception $e){
                $image->create([
                    "owner_id"=>$this->id,"media_type"=>"image","owner_type"=>"product",
                    "folder"=>"products",
                    "filename"=>$pi["basename"],
                    "mime_type"=>"",
                    "title"=>$this->title,
                    "ordering"=>"0","param"=>""
                ]);
            }
        }
        if(count($urls)){
            $http = new HTTP;
            $tut = $this;
            $http->multiFetch($urls,function($calldata)use($tut){
                $pu = parse_url($calldata["url"]);
                $pi = pathinfo($pu["path"]);
                echo "loaded image ".$pi["basename"]."\n";
                file_put_contents($tut->_cfg["images"]["path"]."original/".$pi["basename"],$calldata["response"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],30,30),$tut->_cfg["images"]["path"]."w30xh30/".$pi["basename"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],36,36),$tut->_cfg["images"]["path"]."w36xh36/".$pi["basename"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],120,120),$tut->_cfg["images"]["path"]."w120xh120/".$pi["basename"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],200,200),$tut->_cfg["images"]["path"]."w200xh200/".$pi["basename"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],200,200),$tut->_cfg["images"]["path"]."wxh200/".$pi["basename"]);
                imagejpeg($this->resize_image($tut->_cfg["images"]["path"]."original/".$pi["basename"],350,350),$tut->_cfg["images"]["path"]."w350xh350/".$pi["basename"]);
            });
        }
        // [images] => Array
        //             (
        //                 [0] => https://tytmodno.com/image/data/0c/0c12b3f6-2684-11e7-ae54-6c626d745e7b.jpeg
        //                 [1] => https://tytmodno.com/image/data/0c/0c12b3f6-2684-11e7-ae54-6c626d745e7b-1.jpeg
        //                 [2] => https://tytmodno.com/image/data/0c/0c12b3f6-2684-11e7-ae54-6c626d745e7b-2.jpeg
        //                 [3] => https://tytmodno.com/image/data/0c/0c12b3f6-2684-11e7-ae54-6c626d745e7b-3.jpeg
        //                 [4] => https://tytmodno.com/image/data/0c/0c12b3f6-2684-11e7-ae54-6c626d745e7b-4.jpeg
        //             )
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
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }
};
?>
