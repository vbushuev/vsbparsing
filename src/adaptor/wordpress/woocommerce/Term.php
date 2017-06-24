<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
class Term extends Table{
    protected $fillable = ["name","slug","term_group"];
    protected $_cfg;
    public function __construct($a=null){
        parent::__construct('terms','term_id');
        if(!is_null($a)){
            try{
                $this->find(['name'=>$a["value"]]);
                Log::debug( "Term found #".$this->term_id);
            }
            catch(\Exception $e){
                $slug = Strings::transcript($a["value"]);
                $this->create(["name"=>$a["value"],"slug"=>$slug]);
                Log::debug( "Term created #".$this->term_id);
            }
        }
    }
};
?>
