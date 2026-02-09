<?php 
require 'connection.php';
date_default_timezone_set("Asia/Jakarta");
$today   = date("ymd");

$sql = "SELECT * from tbl_antrian where type='CS' and st_antrian='2' and tgl_antri='$today' order by id_antrian DESC limit 1;"; 
$result = $conn->query($sql);
// output data of each row
if($row = $result->fetch_assoc()) {
    $respon = $row['antrian'];
}else{
    $sql = "SELECT * from tbl_antrian where type='CS' and st_antrian='3' and tgl_antri='$today' order by id_antrian DESC limit 1;"; 
	$result = $conn->query($sql);
	// output data of each row
	if($row = $result->fetch_assoc()) {
		$respon = $row['antrian'];
	}else{
		$respon = "CS-00";
	}
}

$sql_tl = "SELECT * from tbl_antrian where type='Teller' and st_antrian='2' and tgl_antri='$today' order by id_antrian DESC limit 1;"; 
$res_tl = $conn->query($sql_tl);
// output data of each row
if($row = $res_tl->fetch_assoc()) {
    $res_tl = $row['antrian'];
}else{
    $sql_tl = "SELECT * from tbl_antrian where type='Teller' and st_antrian='3' and tgl_antri='$today' order by id_antrian DESC limit 1;"; 
	$res_tl = $conn->query($sql_tl);
	// output data of each row
	if($row = $res_tl->fetch_assoc()) {
		$res_tl = $row['antrian'];
	}else{
		$res_tl = "TL-00";
	}
}

echo json_encode(array('cs_antri' => $respon, 'tl_antri' => $res_tl));

?>