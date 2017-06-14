<?php
namespace adaptor\opencart;
use core\Log as Log;
use db\Table as Table;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use core\objects\Product as coreProduct;
class ProductDescription extends Table{
    protected $fillable = ['product_id',"language_id","name","description","meta_description","meta_keyword","tag"];
    protected $_cfg;
    public function __construct(coreProduct $prd = null){
        parent::__construct('product_description');
        $this->_cfg = Config::opencart();
        if($prd!=null){
            $new_data=[
                "product_id"=>$prd->id,
                "language_id"=>"1",
                "name"=>$prd->title,
                "description"=>htmlspecialchars(preg_replace("/\s*\r?\n\s*/m","",$prd->description)),
                "meta_description"=>"",
                "meta_keyword"=>"",
                "tag"=>""
            ];
            try{
                $this->find(['product_id'=>$prd->id]);
                $this->update($new_data);
            }
            catch(\Exception $e){
                $this->create($new_data);
            }
        }
    }
};
?>
