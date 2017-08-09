<?php
namespace carbazar;
use db\Table as Table;
class Account extends Table{
    protected $fillable = ["name","quantity"];
    protected $_cfg;
    public function __construct(){
        parent::__construct('accounts','id','created_at','updated_at');
    }
    public function decrease(){
        $this->quantity--;
        $this->save();
    }
};
?>
