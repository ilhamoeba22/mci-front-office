<?php
// Test Legacy Token Exact Copy

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
  $info = curl_getinfo($curl);
  $err = curl_error($curl);
  curl_close($curl);
  
  echo "HTTP Code: " . $info['http_code'] . "\n";
  if ($err) echo "Curl Error: $err\n";
  echo "Response: " . $response . "\n";
}

toke();
