<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
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
          <h2>Tarik Tunai</h2>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
          <div class="col-lg-12" data-aos="fade-left" data-aos-delay="100">
            <form action="cek_nama.php" method="post" role="form">
              <div class="row">
                <div class="col-md-4 form-group mt-3">
                    <label for="no_rek">No Rekening</label>
                    <input type="text" name="cek_norek" class="form-control numeric" id="cek_norek" placeholder="No Rekening" required >
                    <input type="hidden" name="jenis" class="form-control" id="jenis" value="tarik" required >
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
                echo "SILAKAN MELANJUTKAN ISI FORM PENARIKAN ANDA"; 
                ?>
              <hr>
              <form action="tarikeun.php" method="post" role="form">
                <div class="row">
                  <div class="col-md-3 form-group mt-3">
                      <label for="">hari, Tanggal</label>
                      <input type="text" class="form-control" readonly name="tgl" id="tgl" value="<?php echo tgl_indo(date("Y-m-d")); ?>" required>
                  </div>
                  <div class="col-md-2 form-group mt-3">
                      <label for="no_rek">No Rekening</label>
                      <input type="text" name="no_rek" class="form-control numeric" id="no_rek" required readonly 
                      value="<?php if(empty($no_rek)){ echo "Kosong"; }else{ echo $no_rek; }  ?>">
                  </div>
                  <div class="col-md-5 form-group mt-3">
                      <label for="nama">Nama Rekening</label>
                      <input type="text" name="nama" class="form-control huruf" id="nama" required readonly
                      value="<?php if(empty($nama_nas)){ echo "Kosong"; }else{ echo $nama_nas; }  ?>">
                  </div>
                  <div class="col-md-2 form-group mt-3">
                      <label for="saldo">Saldo Saat Ini</label>
                      <input type="password" name="saldo" class="form-control huruf" id="saldo"  required disabled
                      value="<?php if(empty($akhir)){ echo "Kosong"; }else{ echo number_format($akhir,2,",","."); }  ?>">

                      <input type="hidden" name="saldo1" class="form-control" id="saldo1"  required 
                      value="<?php if(empty($akhir)){ echo 0; }else{ echo $akhir; }  ?>">
                      <input type="hidden" name="minimum" class="form-control huruf" id="minimum" required 
                      value="<?php if(empty($minimum)){ echo "Kosong"; }else{ echo $minimum; }  ?>">
                  </div>
                  
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
                  <div class="col-md-4 form-group mt-3">
                      <label for="tujuan">Tujuan Transaksi (max 50 karakter)</label>
                      <input type="text" name="tujuan" class="form-control kalimat" id="tujuan" placeholder="Tujuan Transaksi" maxlength="50" required>
                  </div>
                  <div class="col-md-4 form-group mt-3">
                      <label for="nama_penyetor">Nama Penarik</label>
                      <input type="text" name="nama_penyetor" class="form-control huruf" id="nama_penyetor" placeholder="Nama Penarik" required
					  value="<?php if(empty($nama_nas)){ echo "Kosong"; }else{ echo $nama_nas; }  ?>" readonly>
                  </div>
                  <div class="col-md-4 form-group mt-3">
                      <label for="hp_penyetor">No HP Penarik</label>
                      <input type="tel" name="hp_penyetor" class="form-control angkatok" id="hp_penyetor" placeholder="No HP Penarik" required
					  value="<?php if(empty($no_hp)){ echo "Kosong"; }else{ echo $no_hp; }  ?>" readonly>
                  </div>
				  <div class="col-md-6 form-group mt-3">
                      <label for="nama_penyetor">No ID Penarik</label>
                      <input type="text" name="noid_penyetor" class="form-control angkatok" id="noid_penyetor" placeholder="NoID Penarik" required
					  value="<?php if(empty($no_id)){ echo "Kosong"; }else{ echo $no_id; }  ?>" readonly>
                  </div>
                  <div class="col-md-6 form-group mt-3">
                      <label for="hp_penyetor">Alamat Penarik</label>
                      <input type="tel" name="alamat_penyetor" class="form-control kalimat" id="alamat_penyetor" placeholder="Alamat Penarik" required
					  value="<?php if(empty($alamat_)){ echo "Kosong"; }else{ echo $alamat_; }  ?>" readonly>
                  </div>
                </div>
                
                <div class="my-3 mt-3">
                </div>
                <div class="text-center"><button class="btn btn-warning" onclick="cek_saldo('saldo1','nominal1','minimum')" type="submit"><h5>Tarik Tunai</h5></button></div>
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