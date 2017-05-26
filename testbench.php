<?php
include("autoload.php");
use core\Log as Log;
use adaptor\CSVReader as CSVReader;

$csv = new CSVReader;
$r = $csv->get();
print_r($r);
?>
