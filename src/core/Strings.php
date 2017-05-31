<?php
namespace core;
class Strings{
    public static function transcript($s){
        $reps = [
            "/а/u" =>"a",
            "/б/u" =>"b",
            "/в/u" =>"v",
            "/г/u" =>"g",
            "/д/u" =>"d",
            "/е/u" =>"e",
            "/ж/u" =>"dj",
            "/з/u" =>"z",
            "/и/u" =>"i",
            "/й/u" =>"y",
            "/к/u" =>"k",
            "/л/u" =>"l",
            "/м/u" =>"m",
            "/н/u" =>"n",
            "/о/u" =>"o",
            "/п/u" =>"p",
            "/р/u" =>"r",
            "/с/u" =>"s",
            "/т/u" =>"t",
            "/у/u" =>"u",
            "/ф/u" =>"f",
            "/х/u" =>"h",
            "/ч/u" =>"ch",
            "/щ/u" =>"sch",
            "/ш/u" =>"sh",
            "/ь/u" =>"_",
            "/ц/u" =>"ts",
            "/ъ/u" =>"_",
            "/э/u" =>"e",
            "/ю/u" =>"u",
            "/я/u" =>"ya",
            "/ы/u" =>"y",
            "/\s/u" =>"_",
            "/[\%;:\?\!\)\(\[\]\^\&<>\/]/u" =>"",
        ];
        return preg_replace(array_keys($reps), array_values($reps), mb_strtolower($s));
    }
};
?>
