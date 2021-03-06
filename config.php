<?php
$cbConfig = [
    "CSVReader" =>[
        "path" => "input/",
        "separator" => ";",
        "encoding" => "cp1251"
    ],
    "rucaptcha" => [
        "key" => "ccca532951426427e0afa43b710f683f"
    ],
    "unisend" =>[
        "host" => "https://api.unisender.com/ru/api",
        "api_key" => "67qkxk1xyqe87ekasq37w1ri1ej3hprfuqrf8tna",
        "list_ids" => "10093335"
    ],
    "clientBase_prod" => [
        "key" => "d9db2c21bc7497ab48749655c430995a",
        "host" =>"http://v2.prof-context.ru/api",
        "version" =>"2.0",
        "login" => "admin",
        "reportTable" => "380"
    ],
    "clientBase" => [
        "key" => "615cdb1dd19ef65bcb7c81a97360abc0",
        "host" =>"http://cb.bs2/api",
        "version" =>"1.0",
        "login" => "admin",
        "reportTable" => "280"
    ],
    "ksenmart" => [
        "price"=>[
            "adds"=>"10",
            "type"=>"percent" //absolute,percent
        ],
        "images"=>[
            //"path"=>"../tutmodno/media/com_ksenmart/images/product/original/"
            "path"=>"../tutmodno/media/com_ksenmart/images/products/"
        ]
    ],
    "woocommerce" => [
        "price"=>[
            "rate"=>55,
            "adds"=>"20",
            "type"=>"percent" //absolute,percent
        ],
        "site"=>[
            "url"=>"http://dixipay.bs2"
        ],
        "images"=>[
            //"path"=>"../tutmodno/media/com_ksenmart/images/product/original/"
            "path"=>"../dixipay/wp-content/uploads",
            "cms_path"=>"uploads"
        ]
    ],
    "db_wp"=>[
        "host" => "127.0.0.1",
        "user" => "dixipay",
        "pass" => "dixipay",
        "schema" => "dixipay",
        "prefix" => "dp_"
    ],
    "db_rocklabel_ru"=>[
        "host" => "127.0.0.1",
        "user" => "rocklabel_ru_usr",
        "pass" => "Vct17spX7VWVWnK3",
        "schema" => "rocklabel_ru_db",
        "prefix" => "dp_"
    ],
    "db"=>[
        "host" => "127.0.0.1",
        "user" => "opencart",
        "pass" => "opencart",
        "schema" => "opencart",
        "prefix" => ""
    ],
    "opencart" => [
        "price"=>[
            "rate"=>55,
            "adds"=>"20",
            "type"=>"percent" //absolute,percent
        ],
        "site"=>[
            "url"=>"http://opencart.bs2"
        ],
        "images"=>[
            //"path"=>"../tutmodno/media/com_ksenmart/images/product/original/"
            "path"=>"../opencart.bs2/image/data/smartshoes/",
            "cms_path"=>"data/smartshoes/"
        ]
    ],
    "http_"=>[
        "proxy"=>[
            ["url"=>"127.0.0.1:9150","type"=>"sock4"]
        ]
    ]
];
?>
