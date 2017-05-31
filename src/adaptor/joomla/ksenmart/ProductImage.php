<?php
namespace adaptor\joomla\ksenmart;
use core\Log as Log;
use core\Strings as Strings;
use db\Table as Table;
use core\objects\Product as coreProduct;
use core\objects\Category as coreCategory;
class ProductImage extends Table{
    protected $fillable = ["owner_id","media_type","owner_type","folder","filename","mime_type","title","ordering","params"];
    public function __construct(){
        parent::__construct('ksenmart_files');
    }
};

?>
