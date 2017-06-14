<?php
namespace carbazar;
use db\Table as Table;
class Session extends Table{
    protected $fillable = ["apikey_id","session","client_id"];
    protected $_cfg;
    public function __construct($a = null){
        parent::__construct('sessions','id','created_at','updated_at');
        if(!is_null($a) && is_array($a)){
            if(isset($a["apikey_id"])){
                $this->create([
                    "apikey_id"=>$a["apikey_id"],
                    "client_id"=>$a["client_id"],
                    "session"=>self::generate()
                ]);
            }
            elseif (isset($a["session"])) {
                $this->find(["session"=>$a["session"],"updated_at"=>" > date_add(now(),INTERVAL -10 MINUTE)"]);
            }
        }
    }
    public static function generate(){
        if (function_exists('com_create_guid') === true) return trim(com_create_guid(), '{}');
        return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
};
?>
