<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use core\objects\Product as coreProduct;
class Manufacturer extends Table{
    protected $fillable = ["name","image"];
    public function __construct($a = null){
        parent::__construct('manufacturer',"manufacturer_id");
        if($a!=null){
            $new_data=[
                "product_id"=>$prd->id,
                "language_id"=>"1",
                "name"=>$prd->title,
                "description"=>htmlspecialchars($prd->description),
                "meta_description"=>"",
                "meta_keywords"=>"",
                "tag"=>""
            ];
            try{
                $this->find(['name'=>$a["name"]]);
            }
            catch(\Exception $e){
                $this->create($a);
            }
        }
    }
};
?>
