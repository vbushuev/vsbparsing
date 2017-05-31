<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class ProductPropertyValues extends Table{
    protected $fillable = ["product_id","property_id","value_id","text","price"];
    public function __construct(){
        parent::__construct('ksenmart_product_properties_values');
    }
};

?>
