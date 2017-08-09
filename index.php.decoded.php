<?php
/*
LiveImport (c) MaxD, 2017. Write to liveimport@devs.mx for support and purchase.
*/
 goto c9;
 f8: A1($_GET["¦MB"]);
 F2: $http_cache = true;
 $hash_cache_maxtime = 30 * 60;
 if (!@(!$_GET["HK¤A"])) { goto B2;
 } goto F0;
 Eb: define("B¥ ¤¢@", true);
 bb: require_once ">9@9<'=8FDF";
 if (!(@$_GET["¢¥¤A"] != "E" && !f6())) { goto E4;
 } if (!function_exists("0=L)E)?AI£")) { goto D6;
 } goto dd;
 e8: require bd . "E9A9?C=8FF";
 goto Ff;
 Bd: require "E9" . $da[$_GET["HK¤A"]];
 Ff: goto d5;
 Ae: die("hE£B¢AA£ ?¤(M({$E4}3");
 cf: A0: function a8($a6) { header("?=¤E" . e5() . $a6);
 die;
 } if (!(@$_GET["¢K¤"] == "JII" || @$_GET["H¥J"] == "E£" || @$_GET["HK¤"] == "¤¢A" || @$_GET["@AAJJEJA£"] || @$_GET["=@"] || !d7())) { goto bb;
 } goto Eb;
 F0: aF();
 B2: $b2 = @$ee;
 function Bc() { echo E5();
 } function e5() { return "AN D H@K" . (rand(1, 10000000) * 2 + @$_GET["H@"] % 2) . "¢¥¤";
 } goto Ee;
 d5: require bd . "E>9A9AC<$ DF";
 function b1($name) { goto D8;
 fb: $a9 = "J(.";
 $C2 = str_replace("JFDF", $a9, $C2);
 $C2 = str_replace("", "MF (?D(", $C2);
 $C2 = str_replace("J F(?", "JF (A?", $C2);
 $C2 = str_replace($a9, "J DF", $C2);
 goto F1;
 D8: if (!(substr($name, -4) != "8JF")) { goto bc;
 } $name .= "8JF";
 bc: if (!(file_exists($name) and filemtime($name) > @filemtime(C3 . $name))) { goto cd;
 } $C2 = file_get_contents($name);
 goto fb;
 F1: $c0 = false;
 $Bb = 0;
 de: if (!($Bb = strpos($C2, "<", $Bb))) { goto F8;
 } if (!$c0) { goto Af;
 } goto c2;
 ce: cd: return C3 . $name;
 goto D1;
 c2: $Fe = "(";
 goto F7;
 Af: $Fe = "JFDF(D¤/";
 F7: goto ab;
 ab: $C2 = substr($C2, 0, $Bb) . $Fe . substr($C2, $Bb + 1);
 $c0 = !$c0;
 goto de;
 F8: file_put_contents(C3 . $name, $C2);
 goto ce;
 D1: } goto De;
 Ea: die;
 f9: $E4 = @disk_free_space("");
 if (!($E4 > 1)) { goto A0;
 } if (!(($E4 = round(disk_free_space("8") / 1024 / 1024)) < 50)) { goto cf;
 } goto Ae;
 dd: die(")++-{{,ito-,(C(J(«K¢£O£¤A(=I()");
 D6: a8("E");
 E4: if (empty($_GET["LEAMA"])) { goto F2;
 } goto f8;
 Ee: if (empty($_POST)) { goto Bc;
 } foreach ($_POST as &$C1) { $C1 = str_replace("9", "9", $C1);
 A7: } Dd: Bc: if (@$_GET["H¥JA"]) { goto Bd;
 } goto e8;
 c9: define("+?", ">9¤ 9");
 define("@", getcwd() . "9");
 $da = array("N" => "%=@'8  ", "B@" => "#!BFDF", "¥F@" => ">?FFF", "I£" => "#A@ 8  ", "¤¢A" => "%'F ", "M¬" => "BF? 8FD ", "¤A£J" => "=A&$8FDF", "§@¤" => "A$'=FF", "@" => "?@A F ", "AEJH" => "C%<A8FD ", "§?H" => "&<$8 F", "EFH¤" => "%=% DF", "EF¢¤ " => "A>BCF ", "E£" => " &8  ", "" => "! 8 D ", "C" => "BFAFD ", "§IJ" => "G= DF", "H =?" => "AG$BF ", "I?HE ¤" => "="A F", "IJJCI" => "#>F ", "¤=II" => " =@A  ", "JJ" => "C= F", "J¢A" => "''B"8FD ", "ME¬¢" => "??&8 F", "§E¢@ " => "=BG"8FF");
 if (!($Ad = @$_GET["F"])) { goto f9;
 } require "E>9@" . $da[$Ad];
 goto Ea;
 De: function d7() { $eb = sha1(PHP_VERSION_ID . filemtime("EN8 F"));
 return @$_COOKIE["@"] == $eb;
 }
