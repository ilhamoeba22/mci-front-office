<!--<?php-->
<!-- require 'connection.php';-->
<!--$nama          = $_POST['nama'];-->
<!--$no_rek        = $_POST['no_rek'];-->
<!--$tgl           = $_POST['tgl'];-->
<!--$nominal       = $_POST['nominal1'];-->
<!--$terbilang     = $_POST['terbilang1'];-->
<!--$tujuan        = $_POST['tujuan'];-->
<!--$nama_penyetor = $_POST['nama_penyetor'];-->
<!--$hp_penyetor   = $_POST['hp_penyetor'];-->
<!--$alamat_	   = $_POST['alamat_'];-->
<!--$nama_tujuan          = $_POST['nama_tujuan'];-->
<!--$no_rek_tujuan        = $_POST['no_rek_tujuan'];-->
<!--$bank_tujuan          = $_POST['bank_tujuan'];-->
<!--$berita_tujuan        = $_POST['berita_tujuan'];-->
<!--$jenis_trf            = $_POST['jenis_trf'];-->
<!--$biaya_trf            = $_POST['biaya_trf'];-->
<!--$hp_penerima          = $_POST['hp_penerima'];-->
<!--$alamat_tujuan        = $_POST['alamat_tujuan'];-->
<!--$kota_tujuan          = $_POST['kota_tujuan'];-->
<!--date_default_timezone_set("Asia/Jakarta");-->
<!--$token         = "RTGS-".date("ymdHis");-->



<!--$sql = "INSERT INTO tbl_transfer (token, nama, no_rek, tgl, nominal, terbilang, tujuan, nama_penyetor, hp_penyetor,-->
<!--nama_tujuan, no_rek_tujuan, bank_tujuan, berita_tujuan, alamat_tujuan, kota_tujuan, biaya_trf, jenis_trf, hp_penerima, alamat_)-->
<!--VALUES ('$token', '$nama', '$no_rek', '$tgl', '$nominal', '$terbilang', '$tujuan', '$nama_penyetor', '$hp_penyetor',-->
<!--'$nama_tujuan','$no_rek_tujuan','$bank_tujuan','$berita_tujuan','$alamat_tujuan','$kota_tujuan','$biaya_trf','$jenis_trf', '$hp_penerima', '$alamat_');";-->

<!--$today   = date("ymd");-->
<!--$ss      = "SELECT count(id_antrian) as max_id from tbl_antrian where type='Teller' and tgl_antri='$today'";-->
<!--$result = $conn->query($ss);-->
<!--while($row = $result->fetch_assoc()) {-->
<!--    $max_id = $row['max_id'];-->
<!--}-->
<!--$next = $max_id + 1 ;-->
<!--$next_id = "TL-0".$next;-->
<!--$sql_00 = "INSERT INTO tbl_antrian (tgl_antri, nama_antrian, type, antrian, kode) VALUES ('$today', '$nama', 'Teller', '$next_id', '$token');";-->

<!--if ($conn->query($sql) === TRUE && $conn->query($sql_00) === TRUE) {-->
<!--  print_r($sql);-->
<!--  header("Location: output.php?id='$token'");-->
<!--} else {-->
<!--  echo "Error: " . $sql . "<br>" . $conn->error;-->
<!--}-->

<!--$conn->close();-->


<!--?>-->
