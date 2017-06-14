<?php
namespace carbazar;
use db\Table as Table;
class Apikey extends Table{
    protected $fillable = ["apikey","account_id","quantity"];
    protected $_cfg;
    public function __construct(){
        parent::__construct('apikeys','id','created_at','updated_at');
    }
    public function decrease(){
        $this->quantity --;
        $this->save();
    }
};
?>
