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
    "db"=>[
        "host" => "127.0.0.1",
        "user" => "tutmodno",
        "pass" => "tutmodno",
        "schema" => "tutmodno",
        "prefix" => "061c7_"
    ]
];
?>
