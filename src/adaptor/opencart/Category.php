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
            $cd = new CategoryDescription;
            $new_data=[
                "image"=>"",
                "parent_id"=>($a->parent_id===false)?"0":$a->parent_id[0],
                "top"=>"0",
                "column"=>"0",
                "sort_order"=>"0",
                "status"=>"1"
            ];
            try{
                $cd->find(["name"=>$a->title]);
                $new_data["category_id"] = $cd->category_id;
                $this->find(["category_id"=>$cd->category_id,"parent_id"=>(($a->parent_id===false)?"0":$a->parent_id[0])]);
                $this->category_id = $cd->category_id;
                $this->publicData =$new_data;
                // $this->save();
            }
            catch(\Exception $e){
                $this->create($new_data);
            }

            $a->id = $this->category_id;
            new CategoryDescription($a);
            if($a->url!==false)new CategoryPath($a);
            new CategoryLayout($a);
            new CategoryStore($a);
            // print_r($this->toArray());exit;
        }
    }
};
?>
