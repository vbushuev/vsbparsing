<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
class ProductRelated extends Table{
    protected $fillable = ["product_id","related_id"];
    public function __construct( $a = null){
        parent::__construct('product_related');
        if($a!=null){
            try{
                $this->find($a);
            }
            catch(\Exception $e){
                $this->create($a);
            }
        }
    }
};
?>
