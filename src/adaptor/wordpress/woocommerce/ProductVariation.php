<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
class ProductVariation extends Table{
    protected $size ='';
    protected $_cfg;
    public function __construct(coreProduct $prd = null,$adds=[]){
        parent::__construct('posts','ID','post_date',"post_modified");
        $this->size = $adds["size"];
        $this->_cfg = Config::woocommerce();
        if($prd!=null){
            $post_name = Strings::transcript($prd->title)."-".strtolower($adds["size"]);
            $post_title = $prd->title."-".$adds["size"];
            $new_data=[
                "post_author"=>"1",
                "post_content"=>$prd->description,
                "post_title"=>$post_title,
                "post_excerpt"=>"",
                "post_status"=>"publish",
                "comment_status"=>"open",
                "ping_status"=>"closed",
                "post_password"=>"",
                "post_name"=>$post_name,
                "to_ping"=>"",
                "pinged"=>"",
                "post_modified_gmt"=>date("Y-m-d H:i:s"),
                "post_date_gmt"=>date("Y-m-d H:i:s"),
                "post_content_filtered"=>"",
                "post_parent"=>$adds["parent_id"],
                "guid"=>$adds["guid"],
                "menu_order"=>$adds["order"],
                "post_type"=>"product_variation",
                "post_mime_type"=>"",
                "comment_count"=>0
            ];
            try{
                $this->find(['post_name'=>$post_name]);
                $new_data["ID"] = $this->publicData["ID"];
                $this->fromArray($new_data);
                $this->save();
                Log::debug( "Product Variation#".$this->ID."\t".$post_name."[".$post_title."] updated.");
            }
            catch(\Exception $e){
                $this->create($new_data);
                Log::debug( "Product Variation#".$this->ID."\t".$post_name."[".$post_title."] created.");
            }
            // $productCategory = new ProductCategory;
            // $productCategory->findOrCreate(["product_id"=>$this->id,"category_id"=>$prd->category_id,"is_default"=>"1"]);
            new TermRelationship([
                "id"=>$adds["parent_id"],
                "taxonomy"=>"pa_size",
                "name"=>"size",
                "value"=>$adds["size"]
            ]);
            $this->checkProperties($prd);

        }
    }
    public function checkProperties(coreProduct $prd){
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_variation_description","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sku","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_regular_price","meta_value"=>$prd->price]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_sale_price","meta_value"=>$prd->price]);
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
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_product_image_gallery","meta_value"=>""]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_download_limit","meta_value"=>"-1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_download_expiry","meta_value"=>"-1"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_stock","meta_value"=>"NULL"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_stock_status","meta_value"=>"instock"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_average_rating","meta_value"=>"0"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_rating_count","meta_value"=>"a:0:{}"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_wc_review_count","meta_value"=>"0"]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_downloadable_files","meta_value"=>"a:0:{}"]);

        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"attribute_pa_size","meta_value"=>strtolower($this->size)."-size"]);

        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_price","meta_value"=>$prd->price]);
        $pm = new ProductMeta(["post_id"=>$this->ID,"meta_key"=>"_product_version","meta_value"=>"3.0.7"]);
    }
};
?>
