<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
class AttributeDescription extends Table{
    protected $fillable = ['attribute_id',"language_id","name"];
    public function __construct($a=null){
        parent::__construct('attribute_description');
        if($a!=null && is_array($a)){
            try{
                $this->find(['name'=>$a["name"]]);
                $attr = new Attribute(["attribute_group_id"=>$a["attribute_group_id"]]);
            }
            catch(\Exception $e){
                $attr = new Attribute(["attribute_group_id"=>$a["attribute_group_id"]]);
                $this->create(["attribute_id"=>$attr->attribute_id,"language_id"=>"1","name"=>$a["name"]]);
            }
        }
    }
};
?>
