<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Class CoreBankingService
 * 
 * Layanan untuk menangani integrasi API Core Banking.
 * Mencakup otentikasi token (RSA SHA256) dan pengecekan saldo (HMAC SHA512).
 */
class CoreBankingService
{
    protected $baseUrl;
    protected $partnerId;
    protected $clientId;
    protected $secret;
    protected $privateKey;

    /**
     * Konstruktor: Menginisialisasi konfigurasi dari environment variables.
     */
    public function __construct()
    {
        // Fallback to legacy credentials if config is missing (for local dev matching legacy src)
        $this->baseUrl = config('services.core_bank.base_url') ?: 'https://bprs-mci.mitrasoft.com';
        $this->partnerId = config('services.core_bank.partner_id') ?: 'F14353F6-2B07-5D4E-8D98-424801756DA1';
        $this->clientId = config('services.core_bank.client_id') ?: 'C724DE98-99FA-501F-A928-0CFFD158E498';
        $this->secret = config('services.core_bank.secret') ?: 'D3B806E4-2A67-5A2A-94D1-95773756025E';
        
        $pkConfig = config('services.core_bank.private_key');
        if (empty($pkConfig)) {
            // Legacy Private Key (Hardcoded from coba_sign.php)
            $this->privateKey = "-----BEGIN PRIVATE KEY-----
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
        } else {
            $this->privateKey = str_replace('\n', "\n", $pkConfig);
        }
    }

    /**
     * Mendapatkan Token Otentikasi
     * 
     * Melakukan request token ke API Core Banking menggunakan tanda tangan digital RSA SHA256.
     * 
     * @return array|null Array berisi accessToken dan timestamp, atau null jika gagal.
     * @throws \Exception Jika pembuatan tanda tangan gagal.
     */
    public function getToken()
    {
        // 1. Format Timestamp (Legacy Style: 12-hour 'h' with explicit Timezone)
        // PENTING: Set timezone ke Jakarta agar cocok dengan hardcoded +07:00
        date_default_timezone_set('Asia/Jakarta');
        $timestamp = \date("Y-m-d").'T'.\date("h:i:s")."+07:00";
        $stringToSign = $this->clientId . "|" . $timestamp;

        // 2. Tanda Tangan
        $binarySignature = '';
        // Legacy tidak melakukan regex replace, hanya menggunakan string apa adanya.
        // Kita hanya trim spasi ujung.
        $cleanKey = trim($this->privateKey);
        
        if (!\openssl_sign($stringToSign, $binarySignature, $cleanKey, OPENSSL_ALGO_SHA256)) {
             Log::error('CoreBankingService: OpenSSL Sign Failed');
             return null;
        }
        $signature = \base64_encode($binarySignature);

        // 3. Request Curl Native (Copy-Paste Legacy Logic)
        $curl = \curl_init();
        \curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . '/mci-api/api/v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30, // Increased slightly from 0
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
          "grantType": "client_credentials"
        }',
            CURLOPT_HTTPHEADER => array(
              'X-SIGNATURE: '.$signature,
              'X-TIMESTAMP: '.$timestamp,
              'X-CLIENT-KEY: '.$this->clientId,
              'Content-Type: text/plain',
              'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
            ),
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL for local dev
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = \curl_exec($curl);
        $httpCode = \curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = \curl_error($curl);
        \curl_close($curl);

        if ($err) {
            Log::error('CoreBankingService: Curl Error: ' . $err);
            return null;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
             $data = \json_decode($response, TRUE);
             return [
                 'accessToken' => $data['accessToken'] ?? null,
                 'timestamp' => $timestamp
             ];
        }

        Log::error('CoreBankingService: Token Failed', ['code' => $httpCode, 'response' => $response]);
        return null;
    }

    public function getBalance($accountNo)
    {
        // 1. Dapatkan Token
        $tokenData = $this->getToken();
        if (!$tokenData || empty($tokenData['accessToken'])) {
            return ['status' => 'error', 'message' => 'Gagal mendapatkan token akses'];
        }
        $token = $tokenData['accessToken'];

        // 2. Persiapan Data (Match Legacy)
        $uuid = Str::uuid()->toString();
        // Legacy: match timestamp format exact
        date_default_timezone_set('Asia/Jakarta');
        $timestamp = \date("Y-m-d").'T'.\date("h:i:s")."+07:00";
        
        $body = '{
    "partnerReferenceNo" : "rrn_'.$uuid.'",
    "accountNo" : "'.$accountNo.'",
    "balanceTypes" : ["Balance"],
    "detail": true
}';
        
        $hexBody = \hash('sha256', $body);
        $relativeUrl = '/mci-api/open-api/v1/account/balance-inquiry';
        // HMAC Sign
        $stringToSign = 'POST:' . $relativeUrl . ':' . $token . ':' . $hexBody . ':' . $timestamp;
        $signature = \base64_encode(\hash_hmac('sha512', $stringToSign, $this->secret, true));

        // 3. Request Curl Native
        $curl = \curl_init();
        \curl_setopt_array($curl, array(
          CURLOPT_URL => $this->baseUrl . $relativeUrl,
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
            'X-PARTNER-ID: '.$this->partnerId,
            'X-EXTERNAL-ID: '.$uuid,
            'Authorization: Bearer '.$token,
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
          ),
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = \curl_exec($curl);
        $err = \curl_error($curl);
        $httpCode = \curl_getinfo($curl, CURLINFO_HTTP_CODE);
        \curl_close($curl);

        if ($err) {
             Log::error('CoreBankingService: Curl Balance Error: ' . $err);
             return ['status' => 'error', 'message' => 'Kesalahan koneksi'];
        }

        $data = \json_decode($response, true);
        if (!$data) {
            Log::error('CoreBankingService: Balance Parse Error (Not JSON)', ['code' => $httpCode, 'response' => $response]);
            return ['status' => 'error', 'message' => 'Respon API tidak valid'];
        }
        
        return $data;
    }
}
