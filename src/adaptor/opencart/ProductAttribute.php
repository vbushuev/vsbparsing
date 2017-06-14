<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\objects\Product as coreProduct;
class ProductAttribute extends Table{
    protected $fillable = ["product_id","attribute_id","language_id","text"];
    public function __construct( $a = null){
        parent::__construct('product_attribute');
        if($a!=null){
            $new_data=[
                "product_id"=>$a["product_id"],
                "attribute_id"=>$a["attribute_id"],
                "text"=>$a["text"],
                "language_id"=>"1"
            ];
            try{
                $this->find(["product_id"=>$a["product_id"],"attribute_id"=>$a["attribute_id"]]);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
