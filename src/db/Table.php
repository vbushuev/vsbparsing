<?php
namespace db;
use core\Log;
use core\Config;
use db\Connector as dbConnector;
class Table extends Common{
    protected $table=false;
    protected $primary;
    protected $created_at;
    protected $updated_at;
    protected $idx = false;
    protected $conn=null;
    protected $data=false;
    public function __construct($t,$idxField="id",$created_at="created_at",$updated_at="updated_at"){
        $this->table=$t;
        $this->primary=$t;
        $this->created_at=$created_at;
        $this->updated_at=$updated_at;
        $this->conn = new dbConnector();
    }
    public function __destruct(){
        if($this->connected) $this->conn->close();
    }
    public function find($a=[]){
        $sql = "select * from ".$this->table." where 1";
        if(is_array($a))foreach($a as $f=>$v)$sql.=" and {$k} {$v}";
        else $sql.=" and ".$this->primary." = ".$a;
        $r = $this->conn->select($sql);
        $r = $r[0];
        foreach ($r as $key => $value) {
            if($key==$this->primary){
                $this->idx = $value;
                break;
            }
        }
        $this->publicData = $r;
        return $this;
    }
    public function update($a=[]){
        $sql = "update {$this->table} set {$this->updated_at} = '".date("Y-m-d H:i:s")."'";
        foreach($a as $f=>$v)$sql.=", {$k}={$v}";
        $r = $this->conn->update($sql);
    }
    public function create($a=[]){
        if(isset($a[$this->primary]))unset($a[$this->primary]);
        $a[$this->created_at] = date("Y-m-d H:i:s");
        $sql = "insert into {$this->table}('".join(",",array_keys($a))."') values ('".join("','",array_values($a))."');";
        $r = $this->conn->insert($sql);
        if($r!==true){
            $this->idx = $r;
            $a[$this->primary]=$this->idx;
        }
        $this->publicData = $a;
        return $this;
    }
};
?>
