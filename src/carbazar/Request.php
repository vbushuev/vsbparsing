<?php
namespace carbazar;
use db\Table as Table;
class Request extends Table{
    protected $fillable = ["code","session_id","status","vin","data","message"];
    protected $_cfg;
    public function __construct($a = null){
        parent::__construct('requests','id','created_at','updated_at');
        if(!is_null($a) && is_array($a)){
            $this->create($a);
        }
    }
};
?>
