<?php
header("Content-type: application/json; charset=utf-8");
require 'vendor/autoload.php';
use Ramsey\Uuid\Uuid;
include_once 'coba_sign.php';

if(time() - $_SESSION["login_time_stamp"] >3500)  
{
    session_unset();
    session_destroy();
    redirect("coba_sign.php"); 
}else{
    $token  = $_SESSION["token"] ;
    $date   = $_SESSION["date"] ;
}

// Generate a version 4 (random) UUID object
$uuid4 = Uuid::uuid4();
$uuid   = $uuid4->toString();
//data untuk hashing signature
$no_id   = "01000003";
$body       = '{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "cifNo" : "'.$no_id.'"
}';
$http           = "POST";
$relative_url   = "/mci-api/open-api/v1/account/get-master-cif";
$token          = $_SESSION["token"]; $date = $_SESSION["date"];
$hex_body       = hash('sha256', $body);
//HMAC_SHA512 menggunakan secret-key dengan kombinasi: HTTP METHOD + : + relative_url + : + oauth2 token + : + hex(sha256(body) ) + : + X-TIMESTAMP
$sign_getcif    = base64_encode(hash_hmac('sha512', $http.':'.$relative_url.':'.$token.':'.$hex_body.':'.$date, $secret, true));
$result         = get_cif($no_id, $sign_getcif, $patnerid, $uuid, $token, $date);

$hasil = json_decode($result, true);
var_dump(json_decode($result, true));

// $no_ktp 			= ($hasil["data"]["governmentIdNo"]);
// $nama 				= ($hasil["data"]["accountName"]);
// $phone 				= ($hasil["data"]["phoneNo"]);
// $address			= ($hasil["data"]["address"]);

// echo " \n Nomor ID => ".$no_ktp;
// echo " \n nama => ".$nama;
// echo " \n Alamat => ".$address;
// echo " \n No HP => ".$phone;

?>