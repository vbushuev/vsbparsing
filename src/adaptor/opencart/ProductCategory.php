<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\objects\Product as coreProduct;
class ProductCategory extends Table{
    protected $fillable = ["product_id","category_id"];
    public function __construct( coreProduct $a = null){
        parent::__construct('product_to_category');
        if($a!=null){
            $new_data=[
                "product_id"=>$a->id,
                "category_id"=>$a->category_id
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
