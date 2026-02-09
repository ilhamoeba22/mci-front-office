<?php
use Ramsey\Uuid\Uuid;
function get_nama($cek){
header("Content-type: application/json; charset=utf-8");
require 'vendor/autoload.php';
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
$no_id   = $cek;
$body       = '{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "accountNo" : "'.$no_id.'",
    "balanceTypes" : ["Balance"],
    "detail": true
}';
$http           = "POST";
$relative_url   = "/mci-api/open-api/v1/account/balance-inquiry";
$token          = $_SESSION["token"]; $date = $_SESSION["date"];
$hex_body       = hash('sha256', $body);
//HMAC_SHA512 menggunakan secret-key dengan kombinasi: HTTP METHOD + : + relative_url + : + oauth2 token + : + hex(sha256(body) ) + : + X-TIMESTAMP
$sign_          = base64_encode(hash_hmac('sha512', $http.':'.$relative_url.':'.$token.':'.$hex_body.':'.$date, $secret, true));
$result         = get_balance($no_id, $sign_, $patnerid, $uuid, $token, $date);
$hasil = json_decode($result, true);

// $accountNo 			= ($hasil["accountNo"]);
// $nama 				= ($hasil["name"]);
// $availableBalance 	= ($hasil["accountInfo"][0]["availableBalance"]["value"]);
// $minimalAmount 		= ($hasil["accountInfo"][0]["minimalAmount"]["value"]);
// $holdAmount 	    = ($hasil["accountInfo"][0]["holdAmount"]["value"]);
// echo " \n Nomor Rekening => ".$accountNo;
// echo " \n nama => ".$nama;
// echo " \n saldo => ".$availableBalance;
// echo " \n saldo min => ".$minimalAmount;
// echo " \n saldo hold => ".$holdAmount;
return $hasil;
}

?>