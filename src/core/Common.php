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
};
?>
