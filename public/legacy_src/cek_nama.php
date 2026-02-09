<?php 
require 'connection.php';
require 'api/coba_balance.php';
$cek        = $_POST["cek_norek"];
$jenis      = $_POST["jenis"];
if($jenis != "pinbuk"){
    $hasil = get_nama($cek);
    // print_r($hasil);
    $responseCode 		= ($hasil["responseCode"]);
    if($responseCode != "101") {
        $accountNo 			= ($hasil["accountNo"]);
        $nama 				= ($hasil["name"]);
        $amount 	        = ($hasil["accountInfo"][0]["amount"]["value"]);
        $minimalAmount 		= ($hasil["accountInfo"][0]["minimalAmount"]["value"]);
        $holdAmount 	    = ($hasil["accountInfo"][0]["holdAmount"]["value"]);
        
        $alamat 	    = ($hasil["detailAccount"]["city"]).",".($hasil["detailAccount"]["district"]).",".($hasil["detailAccount"]["subdistrict"]).",".($hasil["detailAccount"]["address"]);
        $nik 	        = ($hasil["detailAccount"]["governmentIdNo"]);
        $no_hp 	        = ($hasil["detailAccount"]["phoneNo"]);
        
        $no_rekening    = $accountNo; 
        $nama_nasabah   = $nama; 
        $saldo_akhir    = $amount - $holdAmount;
        $minimum        = $minimalAmount; 
		$alamat_        = $alamat; 
		$no_id        	= $nik;
		$no_hp          = $no_hp;
		// Simpan data ke session
        $_SESSION['transfer_data'] = [
            'no_rekening'   => $no_rekening,
            'nama_nasabah'  => $nama_nasabah,
            'saldo_akhir'   => $saldo_akhir,
            'minimum'       => $minimum,
            'alamat_'       => $alamat_,
            'no_id'         => $no_id,
            'no_hp'         => $no_hp,
        ];
        // var_dump($_SESSION['transfer_data']);
        // Daftar putih (whitelist) halaman tujuan agar tidak open redirect
        $allowed_pages = ['setor', 'tarik', 'online'];
        
        if (in_array($jenis, $allowed_pages)) {
            header("Location: $jenis.php");
            exit();
        }

//         $vars = array('no_rekening' => $no_rekening, 'nama_nasabah' => $nama_nasabah, 'saldo_akhir' => $saldo_akhir, 'minimum' => $minimum, 'alamat_' => $alamat_, 
// 		'no_id' => $no_id, 'no_hp' => $no_hp,);
//         $querystring = http_build_query($vars);
//         if($jenis == "setor"){
//             header("Location: setor.php?$querystring");
//         }elseif($jenis=="tarik"){
//             header("Location: tarik.php?$querystring");
//         }elseif($jenis=="skn"){
//             header("Location: skn.php?$querystring");
//         }elseif($jenis=="rtgs"){
//             header("Location: rtgs.php?$querystring");
//         }elseif($jenis=="online"){
//             header("Location: online.php?$querystring");
//         }

    }else{
        $vars = array('msg' => "NOMOR REKENING TIDAK DITEMUKAN", );
        $querystring = http_build_query($vars);
        if ($con_server->query($sql) == TRUE) {
            if($jenis == "setor"){
                header("Location: setor.php?$querystring");
            }elseif($jenis=="tarik"){
                header("Location: tarik.php?$querystring");
            }elseif($jenis=="skn"){
                header("Location: skn.php?$querystring");
            }elseif($jenis=="rtgs"){
                header("Location: rtgs.php?$querystring");
            }elseif($jenis=="online"){
                header("Location: online.php?$querystring");
            }
        } else {
            echo "Error: " . $sql . "<br>" . $con_server->error;
        }
    }
}else{
    $cek2        = $_POST["cek_norek2"];
    $sql = "select no_rekening, nama_nasabah, saldo_akhir, minimum from tabung join nasabah on tabung.nasabah_id = nasabah.nasabah_id 
    where no_rekening ='$cek' ;"; 

    $sql2 = "select no_rekening, nama_nasabah from tabung join nasabah on tabung.nasabah_id = nasabah.nasabah_id 
    where no_rekening ='$cek2' ;";

    $hasil_cek = $con_server->query($sql);
    $hasil_cek2 = $con_server->query($sql2);
    if($row = $hasil_cek->fetch_assoc() ) {
        $no = $row['no_rekening']; 
        $nama = $row['nama_nasabah']; 
        $sal = $row['saldo_akhir'];
        $minimum = $row['minimum'];
        if( $row2 = $hasil_cek2->fetch_assoc()){
            $no2 = $row2['no_rekening']; 
            $nama2 = $row2['nama_nasabah']; 
        }
        $vars = array('no' => $no, 'nama' => $nama, 'sal' => $sal, 'minimum' => $minimum, 'no2' => $no2, 'nama2' => $nama2, );
        $querystring = http_build_query($vars);
        if ($con_server->query($sql) == TRUE) {
            header("Location: pinbuk.php?$querystring");
        } else {
            echo "Error: " . $sql . "<br>" . $con_server->error;
        }

    }else{
        $vars = array('msg' => "NOMOR REKENING TIDAK DITEMUKAN", );
        $querystring = http_build_query($vars);
        if ($con_server->query($sql) == TRUE) {
            header("Location: pinbuk.php?$querystring");
        } else {
            echo "Error: " . $sql . "<br>" . $con_server->error;
        }
    }
}

?>
