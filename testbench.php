<?php
include("autoload.php");
use core\Log as Log;
use adaptor\yml\Reader as YMLReader;
use adaptor\joomla\ksenmart\Category as Category;
use adaptor\joomla\ksenmart\Product as Product;

$category = new Category;
$product = new Product;
$images = new ProductImage;
$productCategory = new ProductCategory;
$manufact = new Manufacturers;
$propertyValue = new PropertyValues;
$productPropertyValue = new ProductPropertyValues;
$ksenmart = Config::ksenmart();

$csv = new CSVReader;
$csv->rename = false;
$imports = $csv->get();
$i = 0;
foreach ($imports as $import) {
    ++$i;
    $cat = false;
    $prod = false;
    try{
        $cat = $category->find(["title"=>"like '%".$import["id_category_1"]."%'"]);
    }
    catch(\Exception $e){
        //create category 1
        $cat = $category->create([
            "title"=>$import["id_category_1"],
            "childs_title"=>"",
            "alias"=>Strings::transcript($import["product_uniq_name"]),
            "content"=>"",
            "introcontent"=>"",
            "published"=>"1",
            "hits"=>"0",
            "parent_id"=>"0",
            "ordering"=>$category->count()+1,
            "metatitle"=>"",
            "metadescription"=>"",
            "metakeyword"=>"",
        ]);
    }
    if($cat===false){
        echo "Not maked category_id_1\n";continue;
    }
    try{
        $cat = $category->find(["title"=>"like '%".$import["id_category_2"]."%'"]);
    }
    catch(\Exception $e){
        $cat = $category->create([
            "title"=>$import["id_category_1"],
            "childs_title"=>"",
            "alias"=>Strings::transcript($import["product_uniq_name"]),
            "content"=>"",
            "introcontent"=>"",
            "published"=>"1",
            "hits"=>"0",
            "parent_id"=>$category->id,
            "ordering"=>$category->count()+1,
            "metatitle"=>"",
            "metadescription"=>"",
            "metakeyword"=>"",
        ]);
    }
    if($cat===false){
        echo "Not maked category_id_2\n";continue;
    }
    try{
        $manufact->find(["title"=>" like '%".$import["manufact"]."%'"]);
    }
    catch(\Exception $e){
        $manifact->create([
            "title"=>$import["manufact"],
            "alias"=>$import["manufact"],"content"=>"","introcontent"=>"","country"=>"","ordering"=>$manifact->count()+1,"metatitle"=>"","metadescription"=>"","metakeywords"=>""
        ]);
    }
    try{
        $prod = $product->find(["product_code"=>"like '%".$import["articul"]."%'"]);
        // $import["id_category_1"]
        // $import["id_category_2"]
        // $import["articul"]
        //$import["currency"]
        //$import["warehous"]
        // $import["product_description"]
        $import["product_images"];//check images

        // $import["product_description_1"]
        $import["product_sizes"]; //check properties

        $prod->title = $import["product_uniq_name"];
        $prod->old_price = 0;
        $prod->purchase_price = 0;
        $prod->price_type = "1";
        $prod->price = $import["product_prices"];
        $prod->content = $import["product_description"];
        $prod->introcontent = $import["product_description_1"];
        $prod->in_stock = $import["warehous"];
        $prod->manufacturer = $manufact->id;
        $prod->save();
    }
    catch(\Exception $e){
        //create product
        $product->create([
            "title"=>$import["product_uniq_name"],
            "alias"=>Strings::transcript($import["product_uniq_name"]),
            "price"=>$product->priceAdds($import["product_prices"]),
            "old_price"=>"0",
            "purchase_price"=>"0",
            "price_type"=>"1",
            "content"=>$import["product_description"],
            "introcontent"=>$import["product_description_1"],
            "product_code"=>$import["articul"],
            "in_stock"=>$import["warehous"],
            "product_unit"=>"0",
            "product_packaging"=>"0.0000",
            "manufacturer"=>$manufact->id,
            "promotion"=>"0",
            "recommendation"=>"0",
            "hot"=>"0",
            "new"=>"1",
            "published"=>"1",
            "hits"=>"0",
            "carted"=>"0",
            "ordering"=>$product->count()+1,
            "metatitle"=>"",
            "metadescription"=>"",
            "metakeywords"=>"",
            "is_parent"=>"0",
            "type"=>"product",
            "tag"=>""

        ]);
    }
    if($prod===false){
        echo "Not maked product".json_encode($import)."\n";continue;
    }
    try{
        $images->find(["filename"=>" like '%".$import["product_images"]."%'"]);
        if($images->owner_id != $product->id){
            $images->update(["owner_id"=>$prod->id]);
        }
    }
    catch(\Exception $e){
        //create image
        $images->create([
            "owner_id"=>$product->id,"media_type"=>"image","owner_type"=>"product",
            "folder"=>$ksenmart["images"]["path"],
            "filename"=>$import["product_images"],
            "mime_type"=>"",
            "title"=>$import["product_images"],
            "ordering"=>"0","param"=>""
        ]);
    }
    try{$productCategory->find(["product_id"=> "=".$product->id,"category_id"=>"=".$cat->id]);}
    catch(\Exception $e){
        $productCategory->create(["product_id"=>$product->id,"category_id"=>$cat->id,"is_default"=>"1"]);
    }
    $product_sizes = preg_split("/;/",$import["product_sizes"]);
    foreach($product_sizes as $size){
        try{$propertyValue->find(["title"=>"=".$size]);}
        catch(\Exception $e){$propertyValue->create(["alias"=>"_".$size,"property_id"=>31,"title"=>$size,"image"=>"","ordering"=>"0"]);}
        $productPropertyValue->findOrCreate(["product_id"=>$product->id,"property_id"=>"31","value_id"=>$propertyValue->id]);
    }
    echo "#{$i}\tadded|updated\t".$product->id."\n";//json_encode($import,JSON_UNESCAPED_UNICODE)."\n";
    //exit;
}


//max product_id = 28660
//max category_id =244
?>
