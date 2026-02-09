<?php
$patnerid = "F14353F6-2B07-5D4E-8D98-424801756DA1";
$secret   = "D3B806E4-2A67-5A2A-94D1-95773756025E";
$clientid = "C724DE98-99FA-501F-A928-0CFFD158E498";
date_default_timezone_set('Asia/Jakarta');
$date = date("Y-m-d").'T'.date("h:i:s")."+07:00";
require 'vendor/autoload.php';
use Ramsey\Uuid\Uuid;

session_start();
function toke() {
    date_default_timezone_set('Asia/Jakarta');
    $date = date("Y-m-d").'T'.date("h:i:s")."+07:00";
    $clientid = "C724DE98-99FA-501F-A928-0CFFD158E498";
    $private_key= "-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCWOu9Aa83eaFVP
Un0mvUeZH10LQFCMLnCLgZjnyBKoQaFxSUZTpSYoIEPD9Q/W948YF1f1Zu+WY57K
WopHWHzNhz/Lm0ff85Fd0qRMHuJ04hjLiPTvCGja6a3tIIf85PpfA2g0ZbKBKkF/
cSPQk/+oK3uSZoqJRwN0ie1/NeifIISoaNKdoiVEIveKFrnTgrLOTNGD1u4yzoei
ij62GQ/VdHv2Xrp2fRVJR5k2pbHjTWrdIKYxplB6We+v7c+j8xjSjIrAaJcJaUMr
lcQlHaygkhKh4JOKvpSBrmv76K3ZxX6HSClsRuiNqFkIPiVd0Wr+Xe0NZbcJlFvW
0oOrx2A/AgMBAAECggEAC/tZV68BdWqm2zO+DZftHPZWam5Pvk555XaJpnrbmhXZ
9XodpNA4Md0Y6okiIUgPXqGF/2mdVEZPxN3hx3z0P0Q5P1j8K412m6AQQI36C854
ocsjlej6y/L1T0NX9UCiA2/3IK63xPuEy9BPRfR34IbPTUbmZ7qFOQdDLPE0w3Qq
J8G4Q3TU6Y7ZKXAVpVTEejQmvEnhjfzOUTETwDgrCuHPLovZ6hugNMxs6ocSVZL3
qn+YFaWhtzV+5x6pYZKO15xYC+piOogVElPwG9vSkNNBFydwngH/bnrEsHxC3OMk
HKfv5dhFAOQGSA3SJ0duukGLNPkC1kF2YLsUzhpQqQKBgQDRDgeKlX8g9QXokxjC
CLvr42qtkERkmDK5hMxX8ZoKMyki07VbBpMJSNWUGELJnTU35/RmpKSyCrj9umkD
DQj+GDOdPVnPZxqS4G0zAL69aTsGDnbWz4vRAuJ/+U9LDeRreE7gT1PFPqn2nt/A
lTk/yi3KdVjkLiVJbl10cMgx2wKBgQC39z7ZlMrj3hZpT/hhafqN9ZQTLhGwJ6Sn
q3XcIAmDvvPsDCF9UJeBks4cF0kN9D11WNPEqDPAxr9ao/6Ht5WymH7aM9d6c4UF
4mW3mTtn3MqWt+xq6ypE6q6TpOjbkKqc1tgmf1XVO3NheimiyGs1QmC+/96ZZQsH
6uh4hVZSbQKBgArhcw0IeORrPFJ9jXVT5QwC+yNrddPShBlZyxTsszrCrOpuIGtL
bU23Z75cgOVjdEijnvnUqenGWxiBokORYx1ufwk5DzqXQC/S1HwqFsNe/b5z9EV7
6egIAWftvu3GHFRnn5tXJaIHf+shG743RhKG4FlAQE8oA7LNtrl/wTuvAoGAPMc+
yHvUHDx/gwOct/JfiQ8dgMizp1MxnwOSyMr82b34sH/BgLljlLd/yOAYjrempmJW
dJ5tmr8O8U9FBGmu13ZyUnzWL+qChFMr7+B8M/BKLklNnnVbXbF7Q+Qz2naNJ4wD
lZR0MyKVGBtYLiOw58OqWvAGBv/PCSqe61KCFnECgYBwWJvURf40FyZZkB6tXnAQ
VRih6kYPPdMWJ7XDwOZPkFNksrVs+oGCpUxEea6IpJ0g0ItFpUI0kntIOwnL/LtY
emXqJ1IY33acvcmi52W9V/B5jMM5sUxExAVdVWOAyF2UA5KUyWxZlinfT9QRWYEn
S9Pkm+qgWGP/S1ycCiJBNg==
-----END PRIVATE KEY-----";
    
    $data = $clientid."|".$date;
    $binary_signature = "";
    openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
    $signature = base64_encode($binary_signature);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://bprs-mci.mitrasoft.com/mci-api/api/v1/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
          "grantType": "client_credentials"
        }',
        CURLOPT_HTTPHEADER => array(
          'X-SIGNATURE: '.$signature,
          'X-TIMESTAMP: '.$date,
          'X-CLIENT-KEY: '.$clientid,
          'Content-Type: text/plain'
        ),
    ));

  $response = curl_exec($curl);
  curl_close($curl);
  $data = json_decode($response, TRUE);
  return $data;
}

$token    = toke();
$_SESSION["login_time_stamp"] = time(); 

// echo "patnerid = ".$patnerid."<br>";
// echo "secret = ".$secret."<br>";
// echo "clientid = ".$clientid."<br>";
// echo "date = ".$date."<br><br>";


// print_r($token);
$_SESSION["token"]            = $token['accessToken'];
$_SESSION["date"]             = $date;


function get_cif($no_cif, $sign_getcif, $patnerid, $uuid, $token, $date){
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://bprs-mci.mitrasoft.com/mci-api/open-api/v1/account/get-master-cif',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "cifNo" : "'.$no_cif.'"
}',
  CURLOPT_HTTPHEADER => array(
    'X-TIMESTAMP: '.$date,
    'X-SIGNATURE: '.$sign_getcif,
    'X-PARTNER-ID: '.$patnerid,
    'X-EXTERNAL-ID: '.$uuid,
    'Authorization: Bearer '.$token,
    'Content-Type: application/json'
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  return $response;
}

function get_balance($no_id, $sign_, $patnerid, $uuid, $token, $date) {
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://bprs-mci.mitrasoft.com/mci-api/open-api/v1/account/balance-inquiry',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "accountNo" : "'.$no_id.'",
    "balanceTypes" : ["Balance"],
    "detail": true
}',
  CURLOPT_HTTPHEADER => array(
    'X-TIMESTAMP: '.$date,
    'X-SIGNATURE: '.$sign_,
    'X-PARTNER-ID: '.$patnerid,
    'X-EXTERNAL-ID: '.$uuid,
    'Authorization: Bearer '.$token,
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
return $response;

}


?>