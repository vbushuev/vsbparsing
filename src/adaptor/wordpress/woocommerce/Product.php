<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
class Product extends Table{
    protected $fillable = ["ID","post_author","post_date","post_date_gmt","post_content","post_title","post_excerpt","post_status","comment_status","ping_status","post_password","post_name","to_ping","pinged","post_modified","post_modified_gmt","post_content_filtered","post_parent","guid","menu_order","post_type","post_mime_type","comment_count"];
    protected $_cfg;
    protected $imageIds=[];
    public function __construct(coreProduct $prd = null){
        parent::__construct('posts','ID','post_date',"post_modified");
        $this->_cfg = Config::woocommerce();
        if($prd!=null){
            $post_name = Strings::transcript($prd->title);
            $new_data=[
                "post_author"=>"1",
                "post_content"=>$prd->description,
                "post_title"=>$prd->title,
                "post_excerpt"=>"",
                "post_status"=>"publish",
                "comment_status"=>"closed",
                "ping_status"=>"closed",
                "post_password"=>"",
                "post_name"=>$post_name,
                "to_ping"=>"",
                "pinged"=>"",
                "post_modified_gmt"=>date("Y-m-d H:i:s"),
                "post_date_gmt"=>date("Y-m-d H:i:s"),
                "post_content_filtered"=>"",
                "post_parent"=>"0",
                "guid"=>"",
                "menu_order"=>"0",
                "post_type"=>"product",
                "post_mime_type"=>"",
                "comment_count"=>0
            ];
            try{
                $this->find(['post_name'=>$post_name]);
                Log::debug( "Product #".$this->ID."\t".$post_name."[".$this->post_title."] updated.");
            }
            catch(\Exception $e){
                $this->create($new_data);
                Log::debug( "Product #".$this->ID."\t".$post_name."[".$this->post_title."] created.");
            }
            $new_data["guid"] = $this->_cfg["site"]["url"]."/?post_type=product&#038;p=".$this->publicData["ID"];
            $new_data["ID"] = $this->publicData["ID"];
            $this->fromArray($new_data);
            $this->save();
            // $productCategory = new ProductCategory;
            // $productCategory->findOrCreate(["product_id"=>$this->id,"category_id"=>$prd->category_id,"is_default"=>"1"]);
            $this->checkImages($prd);
            $this->checkProperties($prd);
            new TermRelationship([
                "id"=>$this->ID,
                "taxonomy"=>"product_type",
                "name"=>"variable",
                "value"=>"variable"
            ]);
        }
    }
    public function checkProperties(coreProduct $prd){
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_review_count","meta_value"=>"0"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_rating_count","meta_value"=>"a:0:{}"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_average_rating","meta_value"=>"0"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_edit_last","meta_value"=>"1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_edit_lock","meta_value"=>"1496843487:1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sku","meta_value"=>$this->ID]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_regular_price","meta_value"=>$this->price]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sale_price","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sale_price_dates_from","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sale_price_dates_to","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"total_sales","meta_value"=>"0"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_tax_status","meta_value"=>"taxable"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_tax_class","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_manage_stock","meta_value"=>"no"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_backorders","meta_value"=>"no"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sold_individually","meta_value"=>"no"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_weight","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_length","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_width","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_height","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_upsell_ids","meta_value"=>"a:0:{}"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_crosssell_ids","meta_value"=>"a:0:{}"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_purchase_note","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_default_attributes","meta_value"=>"a:0:{}"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_virtual","meta_value"=>"no"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_downloadable","meta_value"=>"no"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_product_image_gallery","meta_value"=>join($this->imageIds,",")]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_download_limit","meta_value"=>"-1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_download_expiry","meta_value"=>"-1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_stock","meta_value"=>"NULL"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_stock_status","meta_value"=>"instock"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_product_version","meta_value"=>"3.0.7"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_price","meta_value"=>$prd->price]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_thumbnail_id","meta_value"=>$this->imageIds[0]]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_product_attributes","meta_value"=>'a:1:{s:7:"pa_size";a:6:{s:4:"name";s:7:"pa_size";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}']);
        $order = 0;
        foreach($prd->params["size"] as $size){
            new ProductVariation($prd,["size"=>$size,"order"=>$order++,"guid"=>$this->publicData["guid"],"parent_id"=>$this->ID]);
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
            $filepath = $this->_cfg["images"]["path"]."/".date("Y")."/".date("m")."/";
            $this->checkDirectory($filepath);
            $filename = $tut->ID."-".$i."-".$pi["basename"];
            $filepath.=$filename;
            $guid = $this->_cfg["site"]["url"].preg_replace('#\.\.\/#im',"",$filepath);
            $guid = preg_replace('#dixipay#im',"",$guid);
            $image = new ProductImage(["url"=>$img,"filename"=>$filename,"product_id"=>$this->publicData["ID"],"guid"=>$guid]);
            $this->imageIds[]=$image->ID;
            if(!file_exists($filepath))$urls[$img]=["url"=>$img,"method"=>"GET","data"=>[],"i"=>$i,"id"=>$image->ID];
            $i++;
        }
        if(count($urls)){
            $http = new HTTP;
            $http->multiFetch($urls,function($calldata)use($tut){
                $pu = parse_url($calldata["url"]);
                $pi = pathinfo($pu["path"]);
                $filepath = $tut->_cfg["images"]["path"]."/".date("Y/m/").$tut->ID."-".$calldata["request"]["i"]."-".$pi["basename"];
                file_put_contents($filepath,$calldata["response"]);
                imagejpeg($this->resize_image($filepath,150,150),preg_replace("/\.(\w{2,4})/i","-150x150.$1",$filepath));
                imagejpeg($this->resize_image($filepath,180,180),preg_replace("/\.(\w{2,4})/i","-180x180.$1",$filepath));
                imagejpeg($this->resize_image($filepath,300,300),preg_replace("/\.(\w{2,4})/i","-300x300.$1",$filepath));
                list($width, $height) = getimagesize($filepath);
                $mime = "image/".$pi["extension"];
                $filename = date("Y/m/").$tut->ID."-".$calldata["request"]["i"]."-".$pi["basename"];
                $metadata = 'a:5:{';
                $metadata.= 's:5:"width";i:'.$width.';s:6:"height";i:'.$height.';s:4:"file";s:'.strlen($filename).':"'.$filename.'";';
                $metadata.= 's:5:"sizes";a:3:{';
                list($width, $height) = getimagesize(preg_replace("/\.(\w{2,4})/i","-150x150.$1",$filepath));
                $metadata.= 's:9:"thumbnail";a:4:{';
                $f01 = preg_replace("/\.(\w{2,4})/i","-150x150.$1",$tut->ID."-".$calldata["request"]["i"]."-".$pi["basename"]);
                $metadata.= 's:4:"file";s:'.strlen($f01).':"'.$f01.'";';
                $metadata.= 's:5:"width";i:'.$width.';';
                $metadata.= 's:6:"height";i:'.$height.';';
                $metadata.= 's:9:"mime-type";s:'.strlen($mime).':"'.$mime.'";';
                $metadata.= '}';
                $metadata.= 's:6:"medium";a:4:{';
                $f03 = preg_replace("/\.(\w{2,4})/i","-300x300.$1",$tut->ID."-".$calldata["request"]["i"]."-".$pi["basename"]);
                $metadata.= 's:4:"file";s:'.strlen($f03).':"'.$f03.'";';
                $metadata.= 's:5:"width";i:'.$width.';';
                $metadata.= 's:6:"height";i:'.$height.';';
                $metadata.= 's:9:"mime-type";s:'.strlen($mime).':"'.$mime.'";';
                $metadata.= '}';
                list($width, $height) = getimagesize(preg_replace("/\.(\w{2,4})/i","-180x180.$1",$filepath));
                $metadata.= 's:14:"shop_thumbnail";a:4:{';
                $f02 = preg_replace("/\.(\w{2,4})/i","-150x150.$1",$tut->ID."-".$calldata["request"]["i"]."-".$pi["basename"]);
                $metadata.= 's:4:"file";s:'.strlen($f02).':"'.$f02.'";';
                $metadata.= 's:5:"width";i:'.$width.';';
                $metadata.= 's:6:"height";i:'.$height.';';
                $metadata.= 's:9:"mime-type";s:'.strlen($mime).':"'.$mime.'";';
                $metadata.= '}';
                $metadata.= '}';
                $metadata.= 's:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}';
                $metadata.= '}';
                //Log::debug( "Image metadta: ".$metadata);
                new ProductMeta(["post_id"=>$calldata["request"]["id"],"meta_key"=>"_wp_attachment_metadata","meta_value"=>$metadata]);
                Log::debug( "loaded image ".$calldata["url"]." to ".$filepath);
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
