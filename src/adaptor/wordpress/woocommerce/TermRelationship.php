<?php
namespace adaptor\wordpress\woocommerce;
use core\Log as Log;
use core\Config as Config;
use core\HTTPConnector as HTTP;
use core\Strings as Strings;
use db\Table as Table;
class TermRelationship extends Table{
    protected $fillable = ["object_id","term_taxonomy_id","term_order"];
    protected $_cfg;
    public function __construct($a=null){
        parent::__construct('term_relationships');
        if(!is_null($a)){
            $term = new Term($a);
            $taxonomy = new TermTaxonomy(["term_id"=>$term->term_id,"taxonomy"=>$a["taxonomy"]]);
            try{
                $this->find(['object_id'=>$a["id"],"term_taxonomy_id"=>$taxonomy->term_taxonomy_id]);
                Log::debug( "TermRelation found #".$this->term_id);
            }
            catch(\Exception $e){
                $this->create(['object_id'=>$a["id"],"term_taxonomy_id"=>$taxonomy->term_taxonomy_id,"term_order"=>"0"]);
                Log::debug( "TermRelation created #".$this->term_id);
            }
        }
    }
};
?>
