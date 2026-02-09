<?php

function loadEnv($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Abaikan jika baris adalah komentar (#)
        if (strpos(trim($line), '#') === 0) continue;

        // Pisahkan key dan value berdasarkan tanda sama dengan (=)
        list($name, $value) = explode('=', $line, 2);
        
        $name = trim($name);
        $value = trim($value);

        // Masukkan ke environment variable
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
    }
    return true;
}

// Panggil fungsi untuk memuat file .env
loadEnv(__DIR__ . '/.env');

// Sekarang Anda bisa mengaksesnya seperti ini:
$server = $_ENV['DB_HOST'];
$user   = $_ENV['DB_USERNAME'];
$pass   = $_ENV['DB_PASSWORD'];
$db     = $_ENV['DB_DATABASE'];

// Lanjutkan dengan koneksi mysqli seperti sebelumnya...
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($server, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    die("Maaf, koneksi database gagal.");
}