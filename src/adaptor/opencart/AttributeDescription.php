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
            }
            catch(\Exception $e){

            }
        }
    }
};
?>
