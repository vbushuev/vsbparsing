<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Strings as Strings;
use core\objects\Category as coreCategory;
class CategoryPath extends Table{
    protected $fillable = ["category_id","path_id","level"];
    protected $_cfg;
    public function __construct( coreCategory $a = null){
        parent::__construct('category_path');
        if($a!=null){
            $level = "0";
            if($a->parent_id!==false){
                $new_data=[
                    "category_id"=>$a->id,
                    "path_id"=>$a->parent_id,
                    "level"=>$level,
                ];
                try{$this->find(['category_id'=>$a->id,'path_id'=>$a->parent_id]);}
                catch(\Exception $e){$this->create($new_data);}
                $level="1";
            }
            $new_data=[
                "category_id"=>$a->id,
                "path_id"=>$a->id,
                "level"=>$level,
            ];
            try{$this->find(['category_id'=>$a->id,'path_id'=>$a->id]);}
            catch(\Exception $e){$this->create($new_data);}
        }
    }
};
?>
