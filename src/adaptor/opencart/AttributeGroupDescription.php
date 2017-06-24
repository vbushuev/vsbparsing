<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
class AttributeGroupDescription extends Table{
    protected $fillable = ['attribute_group_id',"language_id","name"];
    public function __construct($a=null){
        parent::__construct('attribute_group_description');
        if($a!=null && is_array($a)){
            try{
                $this->find(['name'=>$a["name"]]);
            }
            catch(\Exception $e){
                $attrg = new AttributeGroup;
                $this->create(["attribute_group_id"=>$attrg->attribute_group_id,"language_id"=>"1","name"=>$a["name"]]);
            }
        }
    }
};
?>
