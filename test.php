<?php
function GUID(){
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
ob_start();


$data = $_GET;
$clickid= (isset($data["clickid"]) && !empty($data["clickid"]) &&strlen($data["clickid"]))?$data["clickid"]:GUID();
$data["clickid"] = $clickid;
$url=isset($data["url"])?$data["url"]:"http://localhost";
$default_affiliat = "0";
$source = "source";
try{
    $conn = new \mysqli("127.0.0.1","test","test","test");
    $sql = "insert into affiliate(time,affiliat_id,client_id,source,clickid,sub) values (";
    $sql.= time();
    $sql.= ",".(isset($data["affiliat_id"])?$data["affiliat_id"]:$default_affiliat);
    $sql.= ",".(isset($data["client_id"])?$data["client_id"]:"0");
    $sql.= ",'".$source."'";
    $sql.= ",'".$clickid."'";
    $sql.= ",".(isset($data["sub"])?$data["sub"]:"sub");
    $sql.= ")";
    // echo $sql;exit;
    if($conn->connect_errno) throw new Exception("No db connection. Error:".$conn->connect_error);
    if(!$conn->query($sql)){
        throw new Exception($sql." execution error: ".$conn->error);
    }
}
catch(Exception $e){
    print_r($e);
}
$url=$url."?". http_build_query($data);
// ob_get_clean();
echo $url;
// header('Location: ' . $url);
?>
