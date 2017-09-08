<?php
namespace source;
use core\Config as Config;
use core\Common as Common;
use core\Log as Log;
class CSVReader extends Common{
    protected $_cfg=[];
    public function __construct(){
        $this->_cfg = Config::CSVReader();
        $this->publicData["rename"] = true;
    }
    public function get(){
        $res = [];
        if(file_exists($this->_cfg["path"]) && is_dir($this->_cfg["path"])){
            $dh = opendir($this->_cfg["path"]);
            while (($file = readdir($dh)) !== false) {
                if(in_array($file,[".",".."])) continue;
                if(filetype($this->_cfg["path"] . $file)!="file") continue;
                $pi = pathinfo($this->_cfg["path"] . $file);
                if($pi["extension"]!="csv")continue;
                $file = $this->_cfg["path"] . $file;
                $res = $this->parse($file);
                break;
            }
            closedir($dh);
        }
        return $res;
    }
    public function parse($file){
        $res=[];$first=true;$keys=[];
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000000, $this->_cfg["separator"] )) !== FALSE) {
                if(isset($this->_cfg["headers"]) && count($this->_cfg["headers"])) $keys=$this->_cfg["headers"];
                else if($first){
                    $first=false;
                    foreach($data as $k)$keys[]=$k;
                    continue;
                }
                $row=[];
                for($i=0;$i<count($data);++$i){
                    $val = $data[$i];
                    if($this->_cfg["encoding"]!="utf8")$val = iconv($this->_cfg["encoding"],"utf8",$val);
                    if(isset($keys[$i]))$row[$keys[$i]]=$val;
                }
                $res[]=$row;
            }
            fclose($handle);
        }
        if($this->publicData["rename"])rename($file,$file.".done");
        return $res;
    }
};
?>
