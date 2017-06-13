<?php
include("autoload.php");
use core\Log as Log;
$file = "input/Doc_39b7adb47fda40ff9a1903e9377cf85b.xml";
$xml = simplexml_load_file($file);
$d = [];
function toBytes($b){
    $r = $b;
    if($b>=1024*1024*1024)$r=(floor(100*$b/(1024*1024*1024))/100)."G";
    else if($b>=1024*1024)$r=(floor(100*$b/(1024*1024))/100)."M";
    else if($b>=1024)$r=(floor(100*$b/(1024))/100)."K";
    $r.="b";
    return $r;
}
foreach ($xml->ds->i as $i) {
    // [d] => 30.05.2017 23:32:37
    //         [n] => Traffic_Category_R900__rmts_ru
    //         [zp] =>
    //         [zv] =>
    //         [s] => HSDPA (3G)
    //         [a] =>
    //         [du] => 2Kb
    //         [c] => 0
    //         [dup] => 2Kb
    //         [f] => 0
    //         [bd] => 31.05.2017 0:05:53
    //         [cur] => 810
    //         [gmt] => +03:00
    $s = strval($i["s"]);
    $t = $i["du"];
    if(preg_match('/\d+kb/i',$t))$t=1024*intval(preg_replace('/[\D]*/m','',$t));
    else if(preg_match('/\d+mb/i',$t))$t=1024*1024*intval(preg_replace('/[\D]*/m','',$t));
    else if(preg_match('/\d+b/i',$t))$t=intval(preg_replace('/[\D]*/m','',$t));
    $d["sum"]= (isset($d["sum"])?$d["sum"]:0)+$t;

    $diff=date_diff(
        date_create_from_format("d.m.Y H:i:s",$i["bd"]),
        date_create_from_format("d.m.Y H:i:s",$i["d"])
    );
    $df = ($diff->i+$diff->h*60+$diff->d*24*60+$diff->m*30*24*60+$diff->y*365*30*24*60);
    $d["time"]=(isset($d["time"])?$d["time"]:0)+$df;

    echo $i["d"]."\t".$i["bd"]."\t".$df."\t".toBytes($t)."\n";
}
echo "\t\t\t\t\t\t".(floor($d["time"]/60).":".floor($d["time"]%60))."\t".toBytes($d["sum"])."\n";

?>
