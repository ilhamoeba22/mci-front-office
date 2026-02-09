<?php
require 'connection.php';

// 1. Ambil data dari POST (Gunakan Null Coalescing Operator ?? untuk menghindari error undefined index)
$nama           = $_POST['nama'] ?? '';
$no_rek         = $_POST['no_rek'] ?? '';
$tgl            = $_POST['tgl'] ?? '';
$nominal        = $_POST['nominal1'] ?? 0;
$terbilang      = $_POST['terbilang1'] ?? '';
$tujuan         = $_POST['tujuan'] ?? '';
$nama_penyetor  = $_POST['nama_penyetor'] ?? '';
$hp_penyetor    = $_POST['hp_penyetor'] ?? '';
$alamat_        = $_POST['alamat_'] ?? '';
$nama_tujuan    = $_POST['nama_tujuan'] ?? '';
$no_rek_tujuan  = $_POST['no_rek_tujuan'] ?? '';
$bank_tujuan    = $_POST['bank_tujuan'] ?? '';
$berita_tujuan  = $_POST['berita_tujuan'] ?? '';
$jenis_trf      = $_POST['jenis_trf'] ?? '';
$biaya_trf      = $_POST['biaya_trf'] ?? '';
$hp_penerima    = $_POST['hp_penerima'] ?? '';
$alamat_tujuan  = "-";
$kota_tujuan    = "-";

date_default_timezone_set("Asia/Jakarta");
$token = "SKN-" . date("ymdHis");
$today = date("ymd");

// Aktifkan mode exception untuk mysqli agar try-catch bekerja maksimal
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Mulai Transaksi Database
    $conn->begin_transaction();

    // --- 1. INSERT KE tbl_transfer ---
    $sql_trf = "INSERT INTO tbl_transfer (token, nama, no_rek, tgl, nominal, terbilang, tujuan, nama_penyetor, hp_penyetor, 
                nama_tujuan, no_rek_tujuan, bank_tujuan, berita_tujuan, alamat_tujuan, kota_tujuan, biaya_trf, jenis_trf, hp_penerima, alamat_) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql_trf);
    // Bind 19 parameter (semua dianggap string "s" untuk keamanan, sesuaikan jika ada decimal/int)
    $stmt1->bind_param("sssssssssssssssssss", 
        $token, $nama, $no_rek, $tgl, $nominal, $terbilang, $tujuan, $nama_penyetor, $hp_penyetor,
        $nama_tujuan, $no_rek_tujuan, $bank_tujuan, $berita_tujuan, $alamat_tujuan, $kota_tujuan, 
        $biaya_trf, $jenis_trf, $hp_penerima, $alamat_
    );
    $stmt1->execute();

    // --- 2. HITUNG ANTRIAN ---
    $sql_count = "SELECT count(id_antrian) as max_id FROM tbl_antrian WHERE type='Teller' AND tgl_antri=?";
    $stmt2 = $conn->prepare($sql_count);
    $stmt2->bind_param("s", $today);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    
    $next = ($row['max_id'] ?? 0) + 1;
    $next_id = "TL-" . str_pad($next, 2, "0", STR_PAD_LEFT);

    // --- 3. INSERT KE tbl_antrian ---
    $sql_antri = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES (?, ?, 'Teller', ?, ?)";
    $stmt3 = $conn->prepare($sql_antri);
    $stmt3->bind_param("ssss", $today, $nama, $next_id, $token);
    $stmt3->execute();

    // Jika semua perintah di atas sukses, simpan permanen
    $conn->commit();

    // Redirect aman
    header("Location: output.php?id=" . urlencode($token));
    exit();

} catch (Exception $e) {
    // Jika ada yang gagal, batalkan semua (rollback)
    $conn->rollback();
    
    // Log error untuk kebutuhan debug internal
    error_log("Gagal transaksi: " . $e->getMessage());
    
    // Pesan ramah untuk user
    die("Maaf, terjadi kesalahan saat memproses data. Silakan coba lagi.");
}

$conn->close();
?>