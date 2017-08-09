<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Strings as Strings;
use core\objects\Category as coreCategory;
class CategoryStore extends Table{
    protected $fillable = ["category_id","store_id"];
    protected $_cfg;
    public function __construct( coreCategory $a = null){
        parent::__construct('category_to_store');
        if($a!=null){
            $new_data=[
                "category_id"=>$a->id,
                "store_id"=>"0",
            ];
            try{
                $this->find(['category_id'=>$a->id]);
                $this->update($new_data);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
