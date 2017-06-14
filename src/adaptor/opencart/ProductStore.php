<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\objects\Product as coreProduct;
class ProductStore extends Table{
    protected $fillable = ["product_id","store_id"];
    public function __construct( coreProduct $a = null){
        parent::__construct('product_to_store');
        if($a!=null){
            $new_data=[
                "product_id"=>$a->id,
                "store_id"=>"0"
            ];
            try{
                $this->find($new_data);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
