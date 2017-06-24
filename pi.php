<?php
for($i=1;$i<=71;$i++){
    echo '*/15    *   *   *   *   /usr/bin/php /home/srv76501/sant/santechnika/santechnika.php catalog-'.$i.'.php > logs/cronlog-'.$i.'.log'."\n";
}
exit;
include("catalogs.php");
$i = 1;
foreach ($cats as $cat) {
    file_put_contents('catalog-'.$i.'.php','<?php $cats=array('.var_export($cat,true).');?>');
    $i++;
}
?>


*/15    *   *   *   *   /usr/bin/php /home/srv76501/sant/santechnika/santechnika.php catalog-1.php
