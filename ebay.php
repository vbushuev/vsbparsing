<?php
chdir(dirname(__FILE__));
include("autoload.php");
use core\Log as Log;
use core\HTTPConnector as Http;
use source\Ebay as Ebay;
use adaptor\wordpress\woocommerce\Product as Product;
$tick = time();
$products_parsed = 0;
Log::$console=false;
Log::$off=true;
$parsedToDay = json_decode(file_get_contents("store/pages-".date("Y-m-d").".json"),true);
echo "\n---- parsed today: ".count($parsedToDay)." ---- \n";
$ebay = new Ebay("T-shirt-Hoarders");
$products = $ebay->getProducts(function($prd){
    $product = new Product($prd);
});
$ebay->push();
echo "\n---- parsed ".$ebay->getParsedCount()." ---- \n";
echo "\n---- script done in ".(time()-$tick)." ---- \n";
// iLU3V7s4Snyek2M4
// eDgVvh37
// pirAf6U4
/*

https://5.45.126.47:8888/#/login


wp:admin:DaNKy^kKApKJ$cD(Hs
admin:wAyIpvoe&FrH4SiQbjC!L&L7

*/

/* SQL delete
DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type in ('product_variation'));
DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type in ('product'));
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type  in ('product_variation'));
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type  in ('product'));
DELETE FROM wp_posts WHERE post_type = 'product_variation';
DELETE FROM wp_posts WHERE post_type = 'product';

DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_parent in (select ID from tmp_delete));
DELETE FROM wp_term_relationships WHERE object_id IN (SELECT ID FROM wp_posts WHERE ID in (select ID from tmp_delete));
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_parent  in (select ID from tmp_delete));
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE ID  in (select ID from tmp_delete));
DELETE FROM wp_posts WHERE post_parent in (select ID from tmp_delete);
DELETE FROM wp_posts WHERE ID in (select ID from tmp_delete);





SQL tags
delete from wp_term_relationships where term_taxonomy_id in (98,99);# 1306 rows affected.
# 1048 rows affected.
insert into wp_term_relationships(object_id,term_taxonomy_id,term_order) select ID,99,0 from wp_posts where post_type='product' and (post_title like '%women%');# 371 rows affected.
# 256 rows affected.
insert into wp_term_relationships(object_id,term_taxonomy_id,term_order) select ID,98,0 from wp_posts where post_type='product' and (post_title like '%men%')# 1050 rows affected.


select * from wp_posts where post_type='product' and not EXISTS (select 1 from wp_term_relationships where wp_posts.ID = wp_term_relationships.object_id and wp_term_relationships.term_taxonomy_id in (98,99))

*/
?>
