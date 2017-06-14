<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use core\objects\Product as coreProduct;
class ProductImage extends Table{
    protected $fillable = ['product_id',"image","sort_order"];
    protected $_cfg;
    public function __construct($a = null){
        parent::__construct('product_image','product_image_id');
        $this->_cfg = Config::opencart();
        if($a!=null){
            $new_data=[
                "product_id"=>$a["product_id"],
                "image"=>$a["image"],
                "sort_order"=>"0"
            ];
            try{
                $this->find(['product_id'=>$a["product_id"],'image'=>$a["image"]]);
                $this->update($new_data);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
