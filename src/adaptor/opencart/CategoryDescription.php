<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\objects\Category as coreCategory;
class CategoryDescription extends Table{
    protected $fillable = ["category_id","language_id","name","description","meta_description","meta_keyword"];
    protected $_cfg;
    public function __construct( coreCategory $a = null){
        parent::__construct('category_description');
        if($a!=null){
            $new_data=[
                "category_id"=>$a->id,
                "language_id"=>"1",
                "name"=>$a->title,
                "description"=>$a->description,
                "meta_description"=>"",
                "meta_keyword"=>""
            ];
            try{
                $this->find(['name'=>$a->title]);
                $this->update($new_data);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
