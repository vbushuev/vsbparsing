<?php
namespace core;
class Common{
    protected $publicData = [];
    public function __set($n,$v){
        if(isset($this->publicData[$n])) $this->publicData[$n] = $v;
    }
    public function __get($n){
        return (isset($this->publicData[$n]))?$this->publicData[$n]:null;
    }
    public function toArray(){
        return $this->publicData;
    }
    public function toJSON($p=JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE){
        return json_encode($this->publicData,$p);
    }
};
?>
