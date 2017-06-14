<?php
namespace carbazar;
use db\Table as Table;
class Client extends Table{
    protected $fillable = ["id","name","login","password","account_id"];
    protected $_cfg;
    public function __construct(){
        parent::__construct('clients','id','created_at','updated_at');
    }
};
?>
