<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
class Attribute extends Table{
    protected $fillable = ['attribute_group_id',"sort_order"];
    public function __construct($a=null){
        parent::__construct('attribute','attribute_id');
        if($a!=null && is_array($a)){
            try{
                if(isset($a["attribute_id"])){
                    $this->find(["attribute_id"=>$a["attribute_id"]]);
                    $this->update(["attribute_group_id"=>$a["attribute_group_id"]]);
                }else throw new \Exception("Error Processing Request", 1);

            }
            catch(\Exception $e){
                $this->create(["sort_order"=>"1","attribute_group_id"=>$a["attribute_group_id"]]);
            }

        }
    }
};
?>
