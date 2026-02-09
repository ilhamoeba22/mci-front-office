<?php
$tgl1     = $_POST['tgl1'];
$tgl2     = $_POST['tgl2'];
require_once 'connection.php';
require_once 'assets/PHPExcel/PHPExcel.php';
require_once 'assets/PHPExcel/PHPExcel/IOFactory.php';
$result = mysqli_query($conn,"SELECT * FROM tbl_antrian where type='CS' and tgl_antri BETWEEN '$tgl1' AND '$tgl2' order by id_antrian ASC;");
$result1 = mysqli_query($conn,"SELECT * from tbl_setor join tbl_antrian on tbl_setor.token = tbl_antrian.kode 
			where tgl_antri BETWEEN '$tgl1' AND '$tgl2' order by id_setor ASC;");
$result2 = mysqli_query($conn,"SELECT * from tbl_tarik join tbl_antrian on tbl_tarik.token = tbl_antrian.kode
			where tgl_antri BETWEEN '$tgl1' AND '$tgl2' order by id_tarik ASC");
$result3 = mysqli_query($conn,"SELECT * from tbl_transfer join tbl_antrian on tbl_transfer.token = tbl_antrian.kode
			where tgl_antri BETWEEN '$tgl1' AND '$tgl2' order by id_trf ASC");
/* Create new PHPExcel object*/
$objPHPExcel = new PHPExcel();

/* Create a first sheet, representing sales data*/
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'List Antrian CS ');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode');
$objPHPExcel->getActiveSheet()->setCellValue('B2', "$tgl1 - $tgl2");
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Hari, Tanggal');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Antrian');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Kode');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Tujuan Datang');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Solusi / Tanggapan');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Created');
$i=4; 
$no=1;
while($row = mysqli_fetch_array($result)) {
	$tgl_antri=$row['tgl_antri']; 
	$antrian=$row['antrian'];
	$kode=$row['kode']; 
	$created=$row['created'];
	$tujuan_datang=$row['tujuan_datang']; 
	$solusi=$row['solusi'];
    $objPHPExcel->getActiveSheet()->setCellValue("A$i","$no");
	$objPHPExcel->getActiveSheet()->setCellValue("B$i",$tgl_antri);
	$objPHPExcel->getActiveSheet()->setCellValue("C$i",$antrian);
    $objPHPExcel->getActiveSheet()->setCellValue("D$i",$kode);
	$objPHPExcel->getActiveSheet()->setCellValue("E$i",$tujuan_datang);
    $objPHPExcel->getActiveSheet()->setCellValue("F$i",$solusi);
    $objPHPExcel->getActiveSheet()->setCellValue("G$i",$created);
$i++; 
$no++;
}
/*Rename sheet*/
$objPHPExcel->getActiveSheet()->setTitle('Antrian CS');


$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'List Setor Tunai ');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode');
$objPHPExcel->getActiveSheet()->setCellValue('B2', "$tgl1 - $tgl2");
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Hari, Tanggal');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Antrian');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Kode');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'No Rekening');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Nama Rekenig');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Nominal');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Berita untuk Penerima');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Tujuan Setor');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Nama Penyetor');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'Np Hp');
$i=4; 
$no=1;
while($row= mysqli_fetch_array($result1)) {
	$tgl_antri=$row['tgl']; 
	$antrian=$row['antrian'];
	$kode=$row['token']; 
	$no_rek=$row['no_rek'];
	$nama=$row['nama']; 
	$nominal=number_format($row['nominal'],2,",",".");
	$berita=$row['berita']; 
	$tujuan=$row['tujuan'];
	$nama_penyetor=$row['nama_penyetor']; 
	$hp_penyetor=$row['hp_penyetor'];
    $objPHPExcel->getActiveSheet()->setCellValue("A$i","$no");
	$objPHPExcel->getActiveSheet()->setCellValue("B$i",$tgl_antri);
	$objPHPExcel->getActiveSheet()->setCellValue("C$i",$antrian);
    $objPHPExcel->getActiveSheet()->setCellValue("D$i",$kode);
    $objPHPExcel->getActiveSheet()->setCellValue("E$i",$no_rek);
	$objPHPExcel->getActiveSheet()->setCellValue("F$i",$nama);
	$objPHPExcel->getActiveSheet()->setCellValue("G$i",$nominal);
    $objPHPExcel->getActiveSheet()->setCellValue("H$i",$berita);
    $objPHPExcel->getActiveSheet()->setCellValue("I$i",$tujuan);
	$objPHPExcel->getActiveSheet()->setCellValue("J$i",$nama_penyetor);
    $objPHPExcel->getActiveSheet()->setCellValue("K$i",$hp_penyetor);
$i++; 
$no++;
}
$objPHPExcel->getActiveSheet()->setTitle('List Setoran');


$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(2);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'List Tarik Tunai ');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode');
$objPHPExcel->getActiveSheet()->setCellValue('B2', "$tgl1 - $tgl2");
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Hari, Tanggal');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Antrian');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Kode');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'No Rekening');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'Nama Rekenig');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Nominal');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Tujuan Setor');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Nama Penyetor');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Np Hp');
$i=4; 
$no=1;
while($row= mysqli_fetch_array($result2)) {
	$tgl_antri=$row['tgl']; 
	$antrian=$row['antrian'];
	$kode=$row['token']; 
	$no_rek=$row['no_rek'];
	$nama=$row['nama']; 
	$nominal=number_format($row['nominal'],2,",",".");
	$tujuan=$row['tujuan'];
	$nama_penarik=$row['nama_penarik']; 
	$hp_penarik=$row['hp_penarik'];
    $objPHPExcel->getActiveSheet()->setCellValue("A$i","$no");
	$objPHPExcel->getActiveSheet()->setCellValue("B$i",$tgl_antri);
	$objPHPExcel->getActiveSheet()->setCellValue("C$i",$antrian);
    $objPHPExcel->getActiveSheet()->setCellValue("D$i",$kode);
    $objPHPExcel->getActiveSheet()->setCellValue("E$i",$no_rek);
	$objPHPExcel->getActiveSheet()->setCellValue("F$i",$nama);
	$objPHPExcel->getActiveSheet()->setCellValue("G$i",$nominal);
    $objPHPExcel->getActiveSheet()->setCellValue("H$i",$tujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("I$i",$nama_penarik);
	$objPHPExcel->getActiveSheet()->setCellValue("J$i",$hp_penarik);
$i++; 
$no++;
}
$objPHPExcel->getActiveSheet()->setTitle('List Tarikan');


$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(3);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'List Transfer ');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode');
$objPHPExcel->getActiveSheet()->setCellValue('B2', "$tgl1 - $tgl2");
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Hari, Tanggal');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Jenis Transfer');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'Antrian');
$objPHPExcel->getActiveSheet()->setCellValue('E3', 'Kode');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'No Rekening');
$objPHPExcel->getActiveSheet()->setCellValue('G3', 'Nama Rekenig');
$objPHPExcel->getActiveSheet()->setCellValue('H3', 'Nominal');
$objPHPExcel->getActiveSheet()->setCellValue('I3', 'Tujuan Transfer');
$objPHPExcel->getActiveSheet()->setCellValue('J3', 'Nama Penyetor');
$objPHPExcel->getActiveSheet()->setCellValue('K3', 'Np Hp Penyetor');

$objPHPExcel->getActiveSheet()->setCellValue('L3', 'No Rekening Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('M3', 'Nama Rekenig Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('N3', 'No HP Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('O3', 'Bank Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('P3', 'Kota Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('Q3', 'Alamat Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('R3', 'Berita Untuk Tujuan');
$objPHPExcel->getActiveSheet()->setCellValue('S3', 'Biaya Transfer');
$i=4; 
$no=1;
while($row= mysqli_fetch_array($result3)) {
	$tgl_antri=$row['tgl']; 
	$antrian=$row['antrian'];
	$jenis_trf=$row['jenis_trf'];
	$kode=$row['token']; 
	$no_rek=$row['no_rek'];
	$nama=$row['nama']; 
	$nominal=number_format($row['nominal'],2,",",".");
	$tujuan=$row['tujuan'];
	$nama_penyetor=$row['nama_penyetor']; 
	$hp_penyetor=$row['hp_penyetor'];

	$no_rek_tujuan=$row['no_rek_tujuan'];
	$nama_tujuan=$row['nama_tujuan']; 
	$hp_penerima=$row['hp_penerima'];
	$bank_tujuan=$row['bank_tujuan']; 
	$biaya_trf=number_format($row['biaya_trf'],2,",",".");
	$kota_tujuan=$row['kota_tujuan'];
	$alamat_tujuan=$row['alamat_tujuan']; 
	$berita_tujuan=$row['berita_tujuan'];

    $objPHPExcel->getActiveSheet()->setCellValue("A$i","$no");
	$objPHPExcel->getActiveSheet()->setCellValue("B$i",$tgl_antri);
	$objPHPExcel->getActiveSheet()->setCellValue("C$i",$jenis_trf);
	$objPHPExcel->getActiveSheet()->setCellValue("D$i",$antrian);
    $objPHPExcel->getActiveSheet()->setCellValue("E$i",$kode);
    $objPHPExcel->getActiveSheet()->setCellValue("F$i",$no_rek);
	$objPHPExcel->getActiveSheet()->setCellValue("G$i",$nama);
	$objPHPExcel->getActiveSheet()->setCellValue("H$i",$nominal);
    $objPHPExcel->getActiveSheet()->setCellValue("I$i",$tujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("J$i",$nama_penyetor);
	$objPHPExcel->getActiveSheet()->setCellValue("K$i",$hp_penyetor);

	$objPHPExcel->getActiveSheet()->setCellValue("L$i",$no_rek_tujuan);
	$objPHPExcel->getActiveSheet()->setCellValue("M$i",$nama_tujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("N$i",$hp_penerima);
    $objPHPExcel->getActiveSheet()->setCellValue("O$i",$bank_tujuan);
	$objPHPExcel->getActiveSheet()->setCellValue("P$i",$kota_tujuan);
	$objPHPExcel->getActiveSheet()->setCellValue("Q$i",$alamat_tujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("R$i",$berita_tujuan);
    $objPHPExcel->getActiveSheet()->setCellValue("S$i",$biaya_trf);
$i++; 
$no++;
}
$objPHPExcel->getActiveSheet()->setTitle('List Transferan');

$objWriter  =   new PHPExcel_Writer_Excel2007($objPHPExcel);
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="list_trans.xlsx"'); //tell browser what's the file name
header('Cache-Control: max-age=0'); //no cache
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
$objWriter->save('php://output');