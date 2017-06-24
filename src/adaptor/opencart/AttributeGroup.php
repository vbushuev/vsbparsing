<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
class AttributeGroup extends Table{
    protected $fillable = ["sort_order"];
    public function __construct($a=null){
        parent::__construct('attribute_group','attribute_group_id');

            if($a!=null && is_array($a) && isset($a["attribute_group_id"])){
                try{
                    $this->find(['attribute_group_id'=>$a["attribute_group_id"]]);
                }
                catch(\Exception $e){
                    $this->create(["sort_order"=>1]);
                }

            }else $this->create(["sort_order"=>1]);
    }

};
?>
