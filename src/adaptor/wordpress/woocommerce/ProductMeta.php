<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
class ProductMeta extends Table{
    protected $fillable = ["post_id","meta_key","meta_value"];
    protected $_cfg;
    public function __construct($a=null){
        parent::__construct('postmeta','meta_id');
        $this->_cfg = Config::woocommerce();
        if(!is_null($a)){
            if(preg_match("/price/im",$a["meta_key"])){
                $a["meta_value"] = $this->priceAdds($a["meta_value"]);
                //echo "product #{$a["post_id"]} price is: ".$a["meta_value"]."\n";
            }
            try{
                $this->find(['post_id'=>$a["post_id"],"meta_key"=>$a["meta_key"]]);
                $this->publicData=$a;
                $this->save();
            }
            catch(\Exception $e){
                $this->create($a);
            }
        }
    }
    public function __set($n,$v){
        if(isset($this->publicData[$n])){
            if(preg_match("/price/im",$n)&& isset($this->_cfg["price"])){
                $v = $this->priceAdds($v);
            }
            $this->publicData[$n] = $v;
        }
    }
    public function priceAdds($p){
        $v = floatval($p);
        if($this->_cfg["price"]["type"]=="percent"){
            $v = $v+ $v*$this->_cfg["price"]["adds"]/100;
        }else $v = $v+$this->_cfg["price"]["adds"];
        if(isset($this->_cfg["price"]["rate"])){
            $v=$v*floatval($this->_cfg["price"]["rate"]);
        }
        return $v;
    }
};
?>
