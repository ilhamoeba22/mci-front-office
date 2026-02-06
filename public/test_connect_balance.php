<?php
// Script to Generate Postman Data for Balance Inquiry
// Account No: 1190100462

error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseUrl = 'https://bprs-mci.mitrasoft.com';
$partnerId = 'F14353F6-2B07-5D4E-8D98-424801756DA1';
$clientId = 'C724DE98-99FA-501F-A928-0CFFD158E498';
$secret = 'D3B806E4-2A67-5A2A-94D1-95773756025E';
// Hardcoded Private Key (Legacy)
$privateKey = "-----BEGIN PRIVATE KEY-----
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

// UUID Polyfill if needed, but we can usage random unique string
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

echo "Generating Headers for POSTMAN...\n\n";

// --- STEP 1: GET TOKEN ---
date_default_timezone_set('Asia/Jakarta');
$timestamp = date("Y-m-d").'T'.date("h:i:s")."+07:00";
$stringToSign = $clientId . "|" . $timestamp;
$binarySignature = '';
$cleanKey = trim($privateKey);
openssl_sign($stringToSign, $binarySignature, $cleanKey, OPENSSL_ALGO_SHA256);
$signature = base64_encode($binarySignature);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $baseUrl . '/mci-api/api/v1/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{ "grantType": "client_credentials" }',
    CURLOPT_HTTPHEADER => array(
      'X-SIGNATURE: '.$signature,
      'X-TIMESTAMP: '.$timestamp,
      'X-CLIENT-KEY: '.$clientId,
      'Content-Type: text/plain'
    ),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
));
$tokenResp = curl_exec($curl);
$tokenInfo = json_decode($tokenResp, true);
curl_close($curl);

if (!isset($tokenInfo['accessToken'])) {
    die("FATAL: Gagal dapat Token. Cek koneksi.\nResponse: $tokenResp\n");
}
$accessToken = $tokenInfo['accessToken'];


// --- STEP 2: PREPARE BALANCE INQUIRY ---
$accountNo = '1190100462'; // User Requested
$uuid = gen_uuid();
$timestamp = date("Y-m-d").'T'.date("h:i:s")."+07:00"; // New timestamp for transaction

$body = '{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "accountNo" : "'.$accountNo.'",
    "balanceTypes" : ["Balance"],
    "detail": true
}';

// Calculate Signature
$hexBody = hash('sha256', $body);
$relativeUrl = '/mci-api/open-api/v1/account/balance-inquiry';
$stringToSign = 'POST:' . $relativeUrl . ':' . $accessToken . ':' . $hexBody . ':' . $timestamp;
$signature = base64_encode(hash_hmac('sha512', $stringToSign, $secret, true));


// --- OUTPUT POSTMAN DATA ---
echo "========================================\n";
echo "   DATA UNTUK POSTMAN (CEK SALDO)      \n";
echo "========================================\n\n";

echo "METHOD: POST\n";
echo "URL   : " . $baseUrl . $relativeUrl . "\n";

echo "\nHEADERS:\n";
echo "Authorization : Bearer " . $accessToken . "\n";
echo "X-TIMESTAMP   : " . $timestamp . "\n";
echo "X-SIGNATURE   : " . $signature . "\n";
echo "X-PARTNER-ID  : " . $partnerId . "\n";
echo "X-EXTERNAL-ID : " . $uuid . "\n";
echo "Content-Type  : application/json\n";

echo "\nBODY (raw JSON):\n";
echo $body . "\n";

echo "\n========================================\n";
echo "   TESTING LOCALLY (PHP REQUEST)        \n";
echo "========================================\n";

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $baseUrl . $relativeUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_HTTPHEADER => array(
        'X-TIMESTAMP: '.$timestamp,
        'X-SIGNATURE: '.$signature,
        'X-PARTNER-ID: '.$partnerId,
        'X-EXTERNAL-ID: '.$uuid,
        'Authorization: Bearer '.$accessToken,
        'Content-Type: application/json'
    ),
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error: " . $err;
} else {
    echo "Response:\n" . $response;
}
