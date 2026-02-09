<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
<link rel="stylesheet" href="assets/js/select2.css">
<?php 
    function tgl_indo($tanggal){
        date_default_timezone_set("Asia/Jakarta");
        $hari = date ("D");
    
        switch($hari){
            case 'Sun':
                $hari_ini = "Minggu";
            break;
    
            case 'Mon':			
                $hari_ini = "Senin";
            break;
    
            case 'Tue':
                $hari_ini = "Selasa";
            break;
    
            case 'Wed':
                $hari_ini = "Rabu";
            break;
    
            case 'Thu':
                $hari_ini = "Kamis";
            break;
    
            case 'Fri':
                $hari_ini = "Jumat";
            break;
    
            case 'Sat':
                $hari_ini = "Sabtu";
            break;
            
            default:
                $hari_ini = "Tidak di ketahui";		
            break;
        }

        $bulan = array (
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $pecahkan = explode('-', $tanggal);
        
        // variabel pecahkan 0 = tahun
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tanggal
    
        return $hari_ini.', '.$pecahkan[2] . ' - ' . $bulan[ (int)$pecahkan[1] ] . ' - ' . $pecahkan[0];
    }
?>

<?php include 'head.php';?>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center header-scrolled">
    <div class="container d-flex align-items-center justify-content-between header-scrolled">

      <h1 class="logo"><a href="index.php">BPRS HIK MCI</a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href=index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="index.php">Home</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  
  <main id="main">

  <?php 
        session_start();
        if (isset($_SESSION['transfer_data'])) {
        $data       = $_SESSION['transfer_data'];
        $no_rek     =  $data['no_rekening']; 
        $nama_nas   =  $data['nama_nasabah']; 
        $akhir      =  $data['saldo_akhir']; 
        $minimum    =  $data['minimum']; 
        $alamat_    =  $data['alamat_']; 
        $no_id      =  $data['no_id']; 
        $no_hp      =  $data['no_hp']; 
        }
    ?>

     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Transfer Online</h2>
        </div>
        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
          <div class="col-lg-12" data-aos="fade-left" data-aos-delay="100">
            <form action="cek_nama.php" method="post" role="form">
              <div class="row">
                <div class="col-md-4 form-group mt-3">
                    <label for="no_rek">No Rekening</label>
                    <input type="text" name="cek_norek" class="form-control numeric" id="cek_norek" placeholder="No Rekening" required >
                    <input type="hidden" name="jenis" class="form-control" id="jenis" value="online" required >
                </div>
                <div class="col-md-6 form-group mt-3">
                    <label for="">Cek No Rekening</label>
                    <div class="row">
                        <div class="col-lg-3"><button class="btn btn-warning btn-sm" type="submit">Cek Data</button></div>
                        <div class="col-lg-6">
                          <a href="hapus_ss.php" class="btn btn-danger btn-sm">Reset Form</a>
                        </div>
                    </div>
                </div>
              </div>
            </form>
            <hr>
            <?php 
              if(!isset($_SESSION['transfer_data'])){ 
                if(empty($_GET['msg'])){ 
                  echo "SILAKAN MENGISIKAN NO REKENING"; 
                }else{
                  echo ($_GET['msg']);
                }
              }else{   
                echo "SILAKAN MELANJUTKAN ISI FORM TRANSFER ANDA"; 
                ?>
              <hr>
                <form action="onlinekeun.php" method="post" role="form">
                    <div class="row">
                        <div class="col-lg-6 card"> 
                            <div class="form-group mt-3">
                                <center><h3>PENGIRIM </h3></center>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 form-group mt-3">
                                    <label for="">hari, Tanggal</label>
                                    <input type="text" class="form-control" readonly name="tgl" id="tgl" value="<?php echo tgl_indo(date("Y-m-d")); ?>" required>
                                </div>
                                <div class="col-lg-6 form-group mt-3">
                                    <label for="no_rek">No Rekening</label>
                                    <input type="text" name="no_rek" class="form-control numeric" id="no_rek" required readonly 
                                    value="<?php if(empty($no_rek)){ echo "Kosong"; }else{ echo $no_rek; }  ?>">
									<input type="hidden" name="alamat_" class="form-control kalimat" id="alamat_" required readonly 
                                    value="<?php if(empty($alamat_)){ echo "Kosong"; }else{ echo $alamat_; }  ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 form-group mt-3">
                                    <label for="nama">Nama Rekening</label>
                                    <input type="text" name="nama" class="form-control huruf" id="nama" required readonly
                                    value="<?php if(empty($nama_nas)){ echo "Kosong"; }else{ echo $nama_nas; }  ?>">
                                </div>
                                <div class="col-md-6 form-group mt-3">
                                    <label for="saldo">Saldo Saat Ini</label>
                                    <input type="password" name="saldo" class="form-control huruf" id="saldo"  required disabled
                                    value="<?php if(empty($akhir)){ echo "Kosong"; }else{ echo number_format($akhir,2,",","."); }  ?>">

                                    <input type="hidden" name="saldo1" class="form-control" id="saldo1"  required 
                                    value="<?php if(empty($akhir)){ echo 0; }else{ echo $akhir; }  ?>">
                                    <input type="hidden" name="minimum" class="form-control huruf" id="minimum" required 
                                    value="<?php if(empty($minimum)){ echo "Kosong"; }else{ echo $minimum; }  ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 form-group mt-3">
                                    <label for="nominal1">Nominal</label>
                                    <input type="tel" name="nominal1" id="nominal1" placeholder="Masukan Nominal" 
                                    onkeyup="terbilang('nominal1','terbilang1')" onKeyPress="var e = event.keyCode; if( e == 0) e = event.which; if (e >=48 && e < 58 || e==8 || e==9 || e==37 || e==39){}else{event.returnValue = false;event.preventDefault();}" 
                                    class="form-control numeric uang" required>
                                </div>
                                <div class="col-md-7 form-group mt-3">
                                    <label for="terbilang1">Terbilang</label>
                                    <textarea name="terbilang1" readonly class="form-control huruf" id="terbilang1"  rows="5" placeholder="Message" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4  form-group mt-1">
                                    <label for="tujuan">Tujuan Transaksi</label>
                                    <input type="text" name="tujuan" class="form-control kalimat" id="tujuan" placeholder="Tujuan Transaksi"  maxlength="50" required>
                                </div>                                
                                <div class="col-md-4 form-group mt-1">
                                    <label for="nama_penyetor">Nama Pengirim</label>
                                    <input type="text" name="nama_penyetor" class="form-control huruf" id="nama_penyetor" placeholder="Nama Pengirim" required
									value="<?php if(empty($nama_nas)){ echo "Kosong"; }else{ echo $nama_nas; }  ?>" readonly>
                                </div>
                                <div class="col-md-4 form-group mt-1">
                                    <label for="hp_penyetor">No HP Pengirim</label>
                                    <input type="tel" name="hp_penyetor" class="form-control angkatok" id="hp_penyetor" placeholder="No HP Pengirim" required 
									value="<?php if(empty($no_hp)){ echo "Kosong"; }else{ echo $no_hp; }  ?>" readonly>
                                </div>
                                <div class="form-group mt-3">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 card"> 
                            <div class="form-group mt-3">
                                <center><h3>PENERIMA </h3></center>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 form-group mt-3">
                                    <label for="nama_tujuan">Nama Rekening Tujuan</label>
                                    <input type="text" name="nama_tujuan" class="form-control huruf" id="nama_tujuan" placeholder="Nama Di Rekening Tujuan" required>
                                </div>
                                <div class="col-lg-6 form-group mt-3">
                                    <label for="no_rek_tujuan">No Rekening Tujuan</label>
                                    <input type="text" name="no_rek_tujuan" class="form-control numeric" id="no_rek_tujuan" placeholder="No Rekening Tujuan" required>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <?php 
                                require 'connection.php';
                                $sql = "SELECT nama_bank, biaya_trf FROM tbl_bank"; 
                                $result = $conn->query($sql);
                                ?>
                                
                                <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
                                
                                <div class="form-group">
                                    <label for="bank_tujuan">Bank Tujuan</label>
                                    <select name="bank_tujuan1" id="bank_tujuan" class="form-control" required placeholder="---- Pilih Bank Tujuan ----">
                                        <option value="">---- Pilih Bank Tujuan ----</option>
                                        <?php 
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                // 3. Keamanan: Gunakan htmlspecialchars untuk mencegah XSS
                                                $nama_bank = htmlspecialchars($row['nama_bank'], ENT_QUOTES, 'UTF-8');
                                                $biaya_trf = htmlspecialchars($row['biaya_trf'], ENT_QUOTES, 'UTF-8');
                                                $val = $nama_bank . "/" . $biaya_trf;
                                                
                                                echo "<option value=\"$val\">$nama_bank</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                                
                                <script>
                                    // Inisialisasi Tom Select
                                    new TomSelect("#bank_tujuan", {
                                        create: false, // User tidak bisa menambah opsi sendiri
                                        sortField: {
                                            field: "text",
                                            direction: "asc"
                                        }
                                    });
                                </script>
                            </div>
                            <div class="form-group mt-3">
                                <label for="berita_tujuan">Berita Untuk Penerima</label>
                                <input type="text" name="berita_tujuan" class="form-control kalimat" id="berita_tujuan" placeholder="Berita Transaksi"  maxlength="50" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group mt-3">
                                    <input type="hidden" name="jenis_trf" class="form-control" id="jenis_trf" value="ONLINE" readonly required>
                                </div>
                                <div class="col-md-6 form-group mt-3">
                                    <input type="hidden" name="hp_penerima" class="form-control angkatok" id="hp_penerima" placeholder="No HP Penerima" required value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="my-3 mt-3">
                    </div>
                    <div class="text-center"><button class="btn btn-warning" onclick="cek_saldo_trf('saldo1','nominal1','minimum')" type="submit"><h5>Transfer Online</h5></button></div>
                </form>
            <?php } ?>
          </div>
        </div>

      </div>
    </section><!-- End Contact Section -->


   

  </main><!-- End #main -->


  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <?php include 'inputan.php';?>
</body>

</html>