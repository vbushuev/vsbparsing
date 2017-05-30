<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class ProductCategory extends Table{
    protected $fillable = ["product_id","category_id","is_default"];
    public function __construct(){
        parent::__construct('ksenmart_products_categories');
    }
};

?>
