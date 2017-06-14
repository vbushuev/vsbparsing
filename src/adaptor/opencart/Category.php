<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use core\objects\Category as coreCategory;
class Category extends Table{
    protected $fillable = ["image","parent_id","top","column","sort_order","status"];
    protected $_cfg;
    public function __construct( coreCategory $a = null){
        parent::__construct('category','category_id',"date_added","date_modified");
        $this->_cfg = Config::opencart();
        if($a!=null){
            $parent=null;
            if(!is_null($a->parent_id)){
                $parent = new Category($a->parent_id);
            }
            $new_data=[
                "image"=>"",
                "parent_id"=>is_null($parent)?"0":$parent->category_id,
                "top"=>"0",
                "column"=>"0",
                "sort_order"=>"0",
                "status"=>"1"
            ];
            try{
                $cd = new CategoryDescription;
                $cd->find(['name'=>$a->title]);
                $this->find(['category_id'=>$cd->category_id]);
            }
            catch(\Exception $e){
                $this->create($new_data);
                $a->id = $this->category_id;
                $cd = new CategoryDescription($a);
            }
        }
    }
};
?>
