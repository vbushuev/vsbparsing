<?php
namespace carbazar\parse;
use core\Log;
class VIN{
    protected $wmis=[];
    protected $vdss=[];
    protected $wmiStore = "admin/wmi.json";
    protected $vdsStore = "admin/vds.json";
    public function __construct(){
        $this->loadCodes();
    }
    public static function stripNonLatin($v){
        $pattern     = ['/а/im','/в/im','/е/im','/к/im','/м/im','/н/im','/о/im','/р/im','/с/im','/т/im','/у/im','/х/im'];
        $replacement = ['a','b','e','k','m','h','o','p','c','t','y','x'];
        $v = preg_replace($pattern,$replacement,$v);
        return strtoupper($v);
    }
    protected static function transliterate($c){
        $s = '0123456789.ABCDEFGH..JKLMN.P.R..STUVWXYZ';
        return strpos($s,$c) % 10;
    }
    protected static function get_check_digit($vin){
        $map = '0123456789X';
        $weights = '8765432X098765432';
        $sum = 0;
        for ($i = 0; $i < 17;$i++) $sum += self::transliterate($vin[$i]) * strpos($map,$weights[$i]);
        return $map[$sum % 11];
    }
    public static function validate($vin){
        //$vin = self::stripNonLatin($vin);
        if (strlen($vin) !== 17) return false;
        return true;
        return self::get_check_digit($vin) === $vin[8];
    }
    public function get($vin){
        $res = [];
        $vin = strtoupper($vin);
        $wmi = substr($vin,0,3);
        $res = isset($this->wmis[$wmi])?$this->wmis[$wmi]:null;
        if(is_null($res))return null;

        if(isset($this->vdss[$res["brand"]])){
            $vds = substr($vin,$this->vdss[$res["brand"]]["model"]["substr"]["start"],$this->vdss[$res["brand"]]["model"]["substr"]["length"]);
            Log::debug($this->vdss[$res["brand"]]["model"]["substr"],$vds);
            $res["model"] = $this->vdss[$res["brand"]]["model"]["data"][$vds];
        }
        return $res;
    }
    protected function loadCodes(){
        $this->wmis = json_decode(file_get_contents($this->wmiStore),true);
        $this->vdss = json_decode(file_get_contents($this->vdsStore),true);
    }
    protected function getYear($vin){
        $years = [
        	"1"=>"1971",
        	"2"=>"1972",
        	"3"=>"1973",
        	"4"=>"1974",
        	"5"=>"1975",
        	"6"=>"1976",
        	"7"=>"1977",
        	"8"=>"1978",
        	"9"=>"1979",
        	"A"=>"1980",
        	"B"=>"1981",
        	"C"=>"1982",
        	"D"=>"1983",
        	"E"=>"1984",
        	"F"=>"1985",
        	"G"=>"1986",
        	"H"=>"1987",
        	"J"=>"1988",
        	"K"=>"1989",
        	"L"=>"1990",
        	"M"=>"1991",
        	"N"=>"1992",
        	"P"=>"1993",
        	"R"=>"1994",
        	"S"=>"1995",
        	"T"=>"1996",
        	"V"=>"1997",
        	"W"=>"1998",
        	"X"=>"1999",
        	"Y"=>"2000",
        	"1"=>"2001",
        	"2"=>"2002",
        	"3"=>"2003",
        	"4"=>"2004",
        	"5"=>"2005",
        	"6"=>"2006",
        	"7"=>"2007",
        	"8"=>"2008",
        	"9"=>"2009",
        	"A"=>"2010",
        	"B"=>"2011",
        	"C"=>"2012",
        	"D"=>"2013",
        	"E"=>"2014",
        	"F"=>"2015",
        	"G"=>"2016",
        	"H"=>"2017",
        	"J"=>"2018",
        	"K"=>"2019",
        	"L"=>"2020",
        	"M"=>"2021",
        	"N"=>"2022",
        	"P"=>"2023",
        	"R"=>"2024",
        	"S"=>"2025",
        	"T"=>"2026",
        	"V"=>"2027",
        	"W"=>"2028",
        	"X"=>"2029",
        	"Y"=>"2030"
        ];
    }
};
?>
