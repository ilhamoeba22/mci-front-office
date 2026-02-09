<!DOCTYPE html>
<html lang="en">
<head> </head>
<body>
<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=list_trans.xls");
	?>

<center>
        <h3 style="text-decoration: underline;">PT. BPRS MITRA CAHAYA INDONESIA </h3>
        <h4><br>Periode : </h4>
	</center>
    <h4 style=""> LIST ANTRIAN CS  </h4>
    <h4>Periode : </h4>
    <!-- /.card-header -->
    <table border="1">
        <thead>
            <tr>
            <th>No</th>
            <th>Hari,_Tanggal</th>
            <th>No_Antrian</th>
            <th>Kode</th>
            <th>Created </th>
            </tr>
        </thead>
        <tbody>
            <?php  $no=1;  
                require 'connection.php';
                $sql_setor = "select * from tbl_antrian where type='CS' order by id_antrian DESC;";
                $result = $conn->query($sql_setor);
                // output data of each row
                while($row1 = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $no++;  ?></td>
                        <td><?php echo $row1['tgl_antri'] ?></td>
                        <td><?php echo $row1['antrian'] ?></td>
                        <td><?php echo $row1['kode'] ?></td>
                        <td><?php echo $row1['created'] ?></td>
                    </tr>
            <?php } ?> 
        </tbody>
    </table>
    <center>
        <h4><br><br></h4>
        <h4><br><br></h4>
    </center>

    <center>
        <h3 style="">LIST TRANSAKSI SETOR TUNAI  </h3>
        <h4><br>Periode : </h4>
	</center>
    <!-- /.card-header -->
    <table border="1">
        <thead>
            <tr>
            <th>No</th>
            <th>Hari,_Tanggal</th>
            <th>No_Antrian</th>
            <th>Kode</th>
            <th>No_Rekening </th>
            <th>Nama_DiRekening</th>
            <th>Nominal</th>
            <th>Berita_Untuk_Penerima</th>
            <th>Tujuan_Setor</th>
            <th>Nama_Penyetor</th>
            <th>No_hp</th>
            </tr>
        </thead>
        <tbody>
            <?php  $no=1;  
                require 'connection.php';
                $sql_setor = "select * from tbl_setor join tbl_antrian on tbl_setor.token = tbl_antrian.kode 
                order by id_setor DESC;";
                $result = $conn->query($sql_setor);
                // output data of each row
                while($row1 = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $no++;  ?></td>
                        <td><?php echo $row1['tgl'] ?></td>
                        <td><?php echo $row1['antrian'] ?></td>
                        <td><?php echo $row1['token'] ?></td>
                        <td><?php echo $row1['no_rek'] ?></td>
                        <td><?php echo $row1['nama'] ?></td>
                        <td><?php echo number_format($row1['nominal'],2,",",".") ?></td>
                        <td><?php echo $row1['berita'] ?></td>
                        <td><?php echo $row1['tujuan'] ?></td>
                        <td><?php echo $row1['nama_penyetor'] ?></td>
                        <td><?php echo $row1['hp_penyetor'] ?></td>
                    </tr>
            <?php } ?> 
        </tbody>
    </table>
    <center>
        <h4><br><br></h4>
        <h4><br><br></h4>
    </center>

    <center>
        <h3 style="">LIST TRANSAKSI TARIK TUNAI  </h3>
        <h4><br>Periode : </h4>
	</center>
    <!-- /.card-header -->
    <table border="1">
        <thead>
            <tr>
            <th>No</th>
            <th>Hari,_Tanggal</th>
            <th>No_Antrian</th>
            <th>Kode</th>
            <th>No_Rekening </th>
            <th>Nama_DiRekening</th>
            <th>Nominal</th>
            <th>Tujuan_Setor</th>
            <th>Nama_Penyetor</th>
            <th>No_hp</th>
            </tr>
        </thead>
        <tbody>
        <?php  $no=1;  
                require 'connection.php';
                $sql = "SELECT * from tbl_tarik join tbl_antrian on tbl_tarik.token = tbl_antrian.kode
                 order by id_tarik DESC;"; 
                $result22 = $conn->query($sql);
                // output data of each row
                while($row1 = $result22->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $no++;  ?></td>
                        <td><?php echo $row1['tgl'] ?></td>
                        <td><?php echo $row1['antrian'] ?></td>
                        <td><?php echo $row1['token'] ?></td>
                        <td><?php echo $row1['no_rek'] ?></td>
                        <td><?php echo $row1['nama'] ?></td>
                        <td><?php echo number_format($row1['nominal'],2,",",".") ?></td>
                        <td><?php echo $row1['tujuan'] ?></td>
                        <td><?php echo $row1['nama_penarik'] ?></td>
                        <td><?php echo "'".$row1['hp_penarik'] ?></td>
                    </tr> 
            <?php } ?>
        </tbody>
    </table>
    <center>
        <h4><br><br></h4>
    </center>



               
             
<!-- page script -->
</body>
</html>