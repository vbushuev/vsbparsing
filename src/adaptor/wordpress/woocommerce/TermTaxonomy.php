<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
class TermTaxonomy extends Table{
    protected $fillable = ["term_id","taxonomy","description","parent","count"];
    protected $_cfg;
    public function __construct($a=null){
        parent::__construct('term_taxonomy','term_taxonomy_id');
        if(!is_null($a)){
            try{
                $this->find(['term_id'=>$a["term_id"]]);
                Log::debug( "TermTaxonomy found #".$this->term_taxonomy_id);
            }
            catch(\Exception $e){
                $this->create(["term_id"=>$a["term_id"],"taxonomy"=>$a["taxonomy"],"description"=>"","parent"=>"0","count"=>0]);
                Log::debug( "TermTaxonomy not found #".$this->term_taxonomy_id);
            }
        }
    }
};
?>
