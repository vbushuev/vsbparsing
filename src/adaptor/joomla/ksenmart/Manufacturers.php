<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class Manufacturers extends Table{
    protected $fillable = ["title","alias","content","introcontent","country","published","ordering","metatitle","metadescription","metakeywords"];
    public function __construct(){
        parent::__construct('ksenmart_manufacturers');
    }
};
?>
