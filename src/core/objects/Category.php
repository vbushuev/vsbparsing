<?php
namespace core\objects;
use core\Common as Common;
class Category extends Common{
    protected $publicData = [
        "id"=>"",
        "external_id"=>"",
        "title" => "",
        "description" =>"",
        "parent_id" => "",
        "images"=>[]
    ];
};
?>
