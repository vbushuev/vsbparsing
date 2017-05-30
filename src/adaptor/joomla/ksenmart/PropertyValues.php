<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class PropertyValues extends Table{
    protected $fillable = ["alias","property_id","title","image","ordering"];
    public function __construct(){
        parent::__construct('ksenmart_property_values');
    }
};

?>
