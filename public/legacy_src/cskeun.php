<?php
require 'connection.php';

date_default_timezone_set("Asia/Jakarta");
// Gunakan "His" (24 jam) untuk menghindari duplikasi token AM/PM
$token = "CS-" . date("ymdHis");
$today = date("ymd");

// Aktifkan pelaporan error strict
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1. Ambil jumlah antrian hari ini menggunakan Prepared Statement
    $sql_count = "SELECT count(id_antrian) as max_id FROM tbl_antrian WHERE type='CS' AND tgl_antri = ?";
    $stmt1 = $conn->prepare($sql_count);
    $stmt1->bind_param("s", $today);
    $stmt1->execute();
    $result = $stmt1->get_result();
    $row = $result->fetch_assoc();

    $max_id = $row['max_id'] ?? 0;
    $next = $max_id + 1;
    
    // Format agar tetap rapi: CS-01, CS-09, CS-10, dst.
    $next_id = "CS-" . str_pad($next, 2, "0", STR_PAD_LEFT);

    // 2. Input antrian baru menggunakan Prepared Statement
    $sql_insert = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES (?, ?, ?, ?, ?);";
    $stmt2 = $conn->prepare($sql_insert);
    
    $nama_antrian = "Antrian CS";
    $type = "CS";
    
    $stmt2->bind_param("sssss", $today, $nama_antrian, $type, $next_id, $token);
    $stmt2->execute();

    // 3. Redirect aman
    header("Location: output.php?id=" . urlencode($token));
    exit();

} catch (Exception $e) {
    // Catat error secara internal
    error_log("Error Antrian CS: " . $e->getMessage());
    
    // Pesan ramah untuk user (tidak membocorkan detail database)
    die("Sistem antrian sedang mengalami gangguan. Silakan coba lagi.");
}

$conn->close();
?>