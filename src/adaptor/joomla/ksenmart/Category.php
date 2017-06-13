<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use db\Table as Table;
use core\Strings as Strings;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class Category extends Table{
    protected $fillable = ["title","childs_title","alias","content","introcontent","published","hits","parent_id","ordering","metatitle","metadescription","metakeyword","external_id"];
    public function __construct(coreCategory $ctg=null){
        parent::__construct('ksenmart_categories');
        if(!is_null($ctg)){
            $parent_id="0";
            try{$parent=$this->find(['external_id'=>"like '%{$ctg->parent_id}%'"]);$parent_id=$this->id;}
            catch(\Exception $e){}
            $this->publicData=[
                "title"=>$ctg->title,
                "childs_title"=>"",
                "alias"=>Strings::transcript($ctg->title),
                "content"=>$ctg->description,
                "introcontent"=>"",
                "published"=>"1",
                "hits"=>"0",
                "parent_id"=>$parent_id,
                "ordering"=>"0",
                "metatitle"=>"",
                "metadescription"=>"",
                "metakeyword"=>"",
                "external_id"=>$ctg->external_id
            ];
            $found = false;
            try{
                // $this->find(['external_id'=>"like '%{$ctg->external_id}%'"]);
                $this->find(['external_id'=>"regexp '%#{$ctg->external_id}%'"]);
                $found = true;
            }
            catch(\Exception $e){
                $this->create($this->publicData);
            }
            if($found){
                $this->save();
            }
        }
    }
};
?>
