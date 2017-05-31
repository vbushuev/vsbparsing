<?php
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as HTTP;
$site1="";
if(file_exists('logs/tnved.html')){
    $site1 = file_get_contents('logs/tnved.html');
}
else $site1 = iconv('cp1251','utf8',file_get_contents('http://www.tks.ru/db/tnved/tree'));
function getdata($url){
    $f = "logs/".date("Y-m-d-H")."-".preg_replace("/[:\/\.]/im","_",$url);
    if(file_exists($f))return file_get_contents($f);
    $d = iconv('cp1251','utf8',file_get_contents($url));
    file_put_contents($f,$d);
    return $d;
}
//echo $site1;
function getlinks($data,$level=0){
    $result = [];
    //$pattern = "/<li\s+class=\"(tnved|inside_tree)\"\s+id=\"([^\"]+)\"[^>]*>(.+?)<\/?li/s";
    $pattern = "/<a.*?onclick=\"return\sopen_thread_jq\('([^']+)'\);?\"[^>]*>(.+?)<\/a>/s";
    if(preg_match_all($pattern,$data,$m)){
        //print_r($m[1]);
        $us=[];
        for($i=0;$i<count($m[0]);++$i){
            $id = preg_replace("/c(\d+)/im","$1",$m[1][$i]);
            if($id!="1955"){
            $title = $m[2][$i];
            $url = "http://www.tks.ru/db/tnved/tree/".$id."/ajax_open";
            echo str_pad("#".$id,$level,"\t",STR_PAD_LEFT)."\t".$title."\n";
            $result[$url]=["title"=>$title,"id"=>$id,"childs"=>getlinks(getdata($url),$level+1)];}

        }
        //
        // $http = new HTTP;
        // $http->multiFetch($us,function($cd)use(&$result){
        //     $response= iconv('cp1251','utf8',$cd["response"]);
        //     $result[$cd["url"]]["childs"]=getlinks($response,$level+1);
        // });
    }
    return $result;
}

$r = getlinks($site1);
print_r($r);
?>
