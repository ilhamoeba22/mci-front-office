<?php 
        require_once('fancyrow.php');
        $iid         = $_GET['iid'];
        $id_ex      = explode("-",$iid);
        $jenis      = $id_ex[0];
        require 'connection.php';
        // echo $jenis."/". $id;  
        if($jenis == "ST"){
            $sql = "select * from tbl_setor where token = '$iid'"; 
            $result = $conn->query($sql);
            $jn = "TANDA BUKTI SETOR TUNAI";
            $tj = "Penyetor";
			$tt = "Teller";
            while($row = $result->fetch_assoc()) { 
                $no_rek     = $row['no_rek']; 
                $nama       = $row['nama']; 
                $p1         = $row['nama_penyetor'];
                $p2         = $row['hp_penyetor']; 
				$p3         = $row['nominal']; 
                $p4         = $row['terbilang'];
                $p5         = $row['tujuan'];
            }
        }elseif($jenis == "TT"){
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
            }
        }elseif($jenis == "CS"){
            $jn = "TANDA BUKTI CS";
            $tj = "CS";
			$tt = "Cs";
            $all_data = $this->db->query("select * from tbl_transfer where token = '$iid' ");
        }else{
            $jn = "TANDA BUKTI TRANSFER";
            $tj = "Pengirim";
			$tt = "Teller";
            $all_data = $this->db->query("select * from tbl_transfer where token = '$iid' ");
        }

        

        
        $pdf=new PDF_FancyRow('l','mm',array(80,140));
        $pdf->SetAutoPageBreak(false, 0.1);
        $pdf->SetLeftMargin(2);
        $pdf->SetRightMargin(1);
        $pdf->AddPage();
		$foto1 = $_SERVER["DOCUMENT_ROOT"].'/mci_paperless/assets/img/logo_400.png';
        $pdf->Image($foto1, 5, 5, 35, 10);
        $pdf->Ln(4);
        $pdf->SetFont('Arial','b',10);
        $pdf->cell(0,10,"Transaksi BPRS HIK MCI",'0','1','L',false);
        $pdf->Ln(2);
        $pdf->SetFont('Arial','b',6);
        $pdf->cell(10,4,"No Rek",'0','0','L',false);
        $pdf->cell(58,4," : ".$no_rek,'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,"No Rek",'0','0','L',false);
        $pdf->cell(58,4," : ".$no_rek,'0','1','L',false);
		
        $pdf->cell(10,4,"Nama",'0','0','L',false);
        $pdf->cell(58,4," : ".$nama,'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,"Nama",'0','0','L',false);
        $pdf->cell(58,4," : ".$nama,'0','1','L',false);
		
        $pdf->cell(10,4,$tj,'0','0','L',false);
        $pdf->cell(58,4," : ".$p1,'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,$tj,'0','0','L',false);
        $pdf->cell(58,4," : ".$p1,'0','1','L',false);
		
        $pdf->cell(10,4,"No Tlp",'0','0','L',false);
        $pdf->cell(58,4," : ".$p2,'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,"No Tlp",'0','0','L',false);
        $pdf->cell(58,4," : ".$p2,'0','1','L',false);
		$newtext = wordwrap($p5, 8, "\n", true);
        $pdf->cell(10,4,"Ket",'0','0','L',false);
        $pdf->cell(58,4," : ".$newtext,'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,"Ket",'0','0','L',false);
        $pdf->cell(58,4," : ".$newtext,'0','1','L',false);
		
        $pdf->cell(10,4,"Nominal",'0','0','L',false);
        $pdf->cell(58,4," : ".number_format($p3,2,",","."),'0','0','L',false);
		$pdf->cell(2,4,"|",'0','0','L',false);
		$pdf->cell(10,4,"Nominal",'0','0','L',false);
        $pdf->cell(58,4," : ".number_format($p3,2,",","."),'0','1','L',false);
		
        date_default_timezone_set("asia/jakarta");
        $pdf->SetFont('Arial','b',7);
        $pdf->Ln(10);
        $pdf->cell(0,4,$jn,'0','1','L',false);
		$pdf->Ln(5);
		$pdf->cell(100,4,$tt,'0','0','R',false);
		$pdf->cell(20,4,$tj,'0','1','R',false);
        $pdf->cell(0,3,'-' ,'0','1','R',false);
        $pdf->Output();
?>