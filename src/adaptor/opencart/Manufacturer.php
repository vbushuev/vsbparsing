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
