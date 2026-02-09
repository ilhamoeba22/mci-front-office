<?php 
 require 'connection.php';
 $jns_trx       = $_POST['jns_trx'];
 $st_antri      = $_POST['st_antri']; 


    if($jns_trx == "cs"){
        $tujuan_datang       = $_POST['tujuan_datang'];
        $solusi              = $_POST['solusi'];
        $id_antrian          = $_POST['id_antrian'];
        $all_data = "UPDATE tbl_antrian set st_antrian='$st_antri', tujuan_datang='$tujuan_datang', solusi='$solusi' where id_antrian = '$id_antrian' ";
        if ($conn->query($all_data) === TRUE) {
            header("Location:list_cs.php?id=$id_antrian");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    }elseif($jns_trx == "setor"){
        $token    = trim($_POST['token']," ");
        $id       = $_POST['id'];
        $all_data = "UPDATE tbl_antrian set st_antrian='$st_antri' where kode ='$token' ";
        print_r($all_data);
        if ($conn->query($all_data) === TRUE) {
            header("Location:list_setor.php?id=$id");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    }elseif($jns_trx == "tarik"){
        $token              = trim($_POST['token']," ");
        $id                 = $_POST['id'];
        $nama_penyetor      = $_POST['nama_penyetor'];
        $nik_penyetor       = $_POST['nik_penyetor'];
        $all_data           = "UPDATE tbl_antrian set st_antrian='$st_antri' where kode = '$token' ";
        $tarik_data         = "UPDATE tbl_tarik set nama_penarik='$nama_penyetor', noid_penarik='$nik_penyetor' where token = '$token' ";
        if ($conn->query($all_data) === TRUE and $conn->query($tarik_data) === TRUE) {
            header("Location:list_tarik.php?id=$id");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }elseif($jns_trx == "trf"){
        $token              = trim($_POST['token']," ");
        $id                 = $_POST['id'];
        $biaya_trf          = $_POST['biaya_trf'];
        $hp_penerima       = $_POST['hp_penerima'];
        $all_data           = "UPDATE tbl_antrian set st_antrian='$st_antri' where kode = '$token' ";
        $trf_data         = "UPDATE tbl_transfer set biaya_trf='$biaya_trf', hp_penerima='$hp_penerima' where token = '$token' ";
        if ($conn->query($all_data) === TRUE and $conn->query($trf_data) === TRUE) {
            header("Location:list_trf.php?id=$id");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }else{
        $token    = trim($_POST['token']," ");
        $id       = $_POST['id'];
        $all_data = "UPDATE tbl_antrian set st_antrian='$st_antri' where kode = '$token' ";
        if ($conn->query($all_data) === TRUE) {
            header("Location:list_trf.php?id=$id");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }  
    
    
?>