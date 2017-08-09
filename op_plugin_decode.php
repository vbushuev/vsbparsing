<?php
$infile = (count($argv))?$argv[1]:false;
if($infile === false) exit;
$outfile = basename($infile).".decoded.php";
$todec = file_get_contents($infile);
$out = preg_replace_callback('/\\\x?([0-9a-f]+)/im',function($m){
    echo $m[1]." -> ".chr($m[1])."\n";
    $ret = chr(intval($m[1]));
    return $ret;
},$todec);
// $out = utf8_encode($out);
$out = iconv('ISO-8859-1', 'UTF-8',$out);
file_put_contents($outfile,$out);
?>
