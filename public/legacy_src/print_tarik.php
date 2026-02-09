<?php 
        require_once('fancyrow.php');
        $iid         = $_GET['iid'];
        $id_ex      = explode("-",$iid);
        $jenis      = $id_ex[0];
        require 'connection.php';
        // echo $jenis."/". $id;
            $sql = "select * from tbl_tarik where token = '$iid'"; 
            $result = $conn->query($sql);
            $jn = "TANDA BUKTI TARIK TUNAI";
            $tj = "Penarik";
			$tt = "Teller";
            while($row = $result->fetch_assoc()) { 
                $no_rek     = $row['no_rek']; 
                $nama       = $row['nama']; 
                $p1         = $row['nama_penarik']; 
                $p2         = $row['hp_penarik']; 
                $p3         = $row['nominal']; 
                $p4         = $row['terbilang'];
                $p5         = $row['tujuan'];
				$p7         = $row['created'];
				$noid_penarik         	= $row['noid_penarik'];
				$alamat_penarik         = $row['alamat_penarik'];
				$nama_penarik         	= $row['nama_penarik']; 
                $hp_penarik		        = $row['hp_penarik']; 
            }

        

        
        $pdf=new PDF_FancyRow('l','mm',array(100,210));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
        $pdf->Ln(21);
        $pdf->Ln(3);
		$date = date_create($p7);
		$tgl  = date_format($date,"d/m/Y");
        $pdf->SetFont('Arial','b',8);
		$pdf->cell(25,4,"",'0','0','L',false);
        $pdf->cell(50,4,$tgl,'0','1','L',false);
		$pdf->Ln(3);
        $pdf->cell(50,4,"",'0','0','L',false);
        $pdf->cell(60,4,"".$nama,'0','0','L',false);
        $pdf->cell(35,4,"",'0','0','L',false);
        $pdf->cell(50,4,"".$no_rek,'0','1','L',false);
		$pdf->Ln(3);
		$pdf->cell(47,4,"",'0','0','L',false);
        $pdf->cell(50,4,number_format($p3,2,",","."),'0','0','L',false);
		$pdf->cell(35,4,"",'0','0','L',false);
		$pdf->MultiCell(70,4, $p4, '0','1');
		$pdf->Ln(2);
		$pdf->cell(35,4,"",'0','0','L',false);
        $pdf->cell(50,4,$p5,'0','0','L',false);
		
		$pdf->AddPage();
        $pdf->Ln(11); // Moved down +3mm (8->11)
        $pdf->SetFont('Arial','b',8);
		$pdf->cell(140,4,"",'0','0','L',false);
        $pdf->cell(50,4,$nama_penarik,'0','1','L',false);
		$pdf->Ln(7);
		$pdf->cell(140,4,"",'0','0','L',false);
        $pdf->cell(50,4,$noid_penarik,'0','1','L',false);
		$pdf->Ln(7);
		$pdf->cell(140,4,"",'0','0','L',false);
        $pdf->cell(50,4,$hp_penarik,'0','1','L',false);
		$pdf->Ln(8);
		$pdf->cell(140,4,"",'0','0','L',false);
        
        // Alamat: Use smaller font and tighter line height (3mm) for better fit
        $pdf->SetFont('Arial','b',7); 
        $pdf->MultiCell(70,3, $alamat_penarik, '0','1');
		
        
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',7);
        $pdf->cell(0,3,'-' ,'0','1','R',false);
        $pdf->Output();
?>