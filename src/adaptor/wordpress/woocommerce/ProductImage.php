<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
class ProductImage extends Table{
    protected $fillable = ["ID","post_author","post_date","post_date_gmt","post_content","post_title","post_excerpt","post_status","comment_status","ping_status","post_password","post_name","to_ping","pinged","post_modified","post_modified_gmt","post_content_filtered","post_parent","guid","menu_order","post_type","post_mime_type","comment_count"];
    protected $_cfg;
    public function __construct($img=null){
        parent::__construct('posts','ID','post_date',"post_modified");
        $this->_cfg = Config::woocommerce();
        if($img!=null){
            $pu = parse_url($img["filename"]);
            $pi = pathinfo($pu["path"]);
            $mime_type = "image/".$pi["extension"];
            $new_data=[
                "post_author"=>"1",
                "post_content"=>"",
                "post_title"=>$pi["filename"],
                "post_excerpt"=>"",
                "post_status"=>"inherit",
                "comment_status"=>"open",
                "ping_status"=>"closed",
                "post_password"=>"",
                "post_name"=>$pi["filename"],
                "to_ping"=>"",
                "pinged"=>"",
                "post_modified_gmt"=>date("Y-m-d H:i:s"),
                "post_date_gmt"=>date("Y-m-d H:i:s"),
                "post_content_filtered"=>"",
                "post_parent"=>$img["product_id"],
                "guid"=>$img["guid"], // ????!??!?!?!?
                "menu_order"=>"0",
                "post_type"=>"attachment",
                "post_mime_type"=>$mime_type,
                "comment_count"=>0
            ];
            $found = false;
            try{
                $this->find(['post_name'=>$pi["filename"]]);
                $this->fromArray($new_data);
                $this->save();
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
            $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wp_attached_file","meta_value"=>date("Y/m/").$img["filename"]]);


        }
    }
};
?>
