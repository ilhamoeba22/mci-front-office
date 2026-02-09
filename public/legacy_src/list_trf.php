<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
<link rel="stylesheet" href="assets/js/select2.css">
<?php 
     $id = $_GET['id'];
     require 'connection.php';
     $sql = "select * from tbl_transfer where id_trf='$id';"; 
     $result = $conn->query($sql);
?>

<?php include 'head_adm.php';?>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center header-scrolled">
    <div class="container d-flex align-items-center justify-content-between header-scrolled">

      <h1 class="logo"><a href="aiueo.php">Bank Syariah MCI</a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href=index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="aiueo.php">Home</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->

  
  <main id="main">

  <form action="list_done.php" method="post" role="form">
  <?php while($row = $result->fetch_assoc()) { ?>
     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Transfer <?php echo $row['jenis_trf']; ?></h2>
        </div>
        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
          <div class="col-lg-12" data-aos="fade-left" data-aos-delay="100">

            
              <div class="row">
                    <div class="col-lg-6 card"> 
                        <div class="form-group mt-3">
                            <center><h3>PENGIRIM </h3></center>
                        </div>
                        <div class="form-group mt-3">
                            <label for="">hari, Tanggal</label>
                            <input type="text" class="form-control" readonly name="tgl" id="tgl" value="<?php echo $row['tgl']; ?>">
                        </div>
                        <div class="row">
                            <div class="col-lg-6 form-group mt-3">
                                <label for="nama">Nama Rekening</label>
                                <input type="text" name="nama" class="form-control huruf" id="nama" value="<?php echo $row['nama']; ?>" readonly>
                            </div>
                            <div class="col-lg-6 form-group mt-3">
                                <label for="no_rek">No Rekening</label>
                                <input type="text" name="no_rek" class="form-control numeric" id="no_rek" value="<?php echo $row['no_rek']; ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group mt-3">
                                <label for="nominal1">Nominal</label>
                                <input type="tel" name="nominal1" id="nominal1" placeholder="Masukan Nominal" 
                                 class="form-control numeric uang" value="<?php echo number_format($row['nominal'],2,".",","); ?>" readonly>
                            </div>
                            <div class="col-md-7 form-group mt-3">
                                <label for="terbilang1">Terbilang</label>
                                <textarea name="terbilang1" readonly class="form-control huruf"  rows="3" readonly><?php echo $row['terbilang']; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group mt-1">
                            <label for="tujuan">Tujuan Transaksi</label>
                            <input type="text" name="tujuan" class="form-control" value="<?php echo $row['tujuan']; ?>" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mt-1">
                                <label for="nama_penyetor">Nama Pengirim</label>
                                <input type="text" name="nama_penyetor" class="form-control huruf" value="<?php echo $row['nama_penyetor']; ?>" readonly>
                            </div>
                            <div class="col-md-6 form-group mt-1">
                                <label for="hp_penyetor">No HP Pengirim</label>
                                <input type="tel" name="hp_penyetor" class="form-control" value="<?php echo $row['hp_penyetor']; ?>" readonly>
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
                                <input type="text" name="nama_tujuan" class="form-control huruf" value="<?php echo $row['nama_tujuan']; ?>" readonly >
                            </div>
                            <div class="col-lg-6 form-group mt-3">
                                <label for="no_rek_tujuan">No Rekening Tujuan</label>
                                <input type="text" name="no_rek_tujuan" class="form-control numeric" value="<?php echo $row['no_rek_tujuan']; ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="bank_tujuan">Bank Tujuan</label>
                            <select name="bank_tujuan" id="bank_tujuan" class="form-control" readonly>                                
                                    <option value="<?php echo $row['bank_tujuan'] ?>"> <?php echo $row['bank_tujuan']; ?> </option>                                
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="berita_tujuan">Berita Untuk Penerima</label>
                            <input type="text" name="berita_tujuan" class="form-control" value="<?php echo $row['berita_tujuan']; ?>" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="alamat_tujuan">Alamat Penerima</label>
                            <input type="text" name="alamat_tujuan" class="form-control" value="<?php echo $row['alamat_tujuan']; ?>" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="kota_tujuan">Kota Tujuan</label>
                            <select name="kota_tujuan" id="kota_tujuan" class="form-control" readonly>
                                    <option value="<?php echo $row['kota_tujuan'] ?>"> <?php echo $row['kota_tujuan']; ?> </option>
                            </select>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 form-group mt-3">
                                <label for="biaya_trf">Biaya Transfer</label>
                                <input type="text" name="biaya_trf" class="form-control" value="<?php echo number_format($row['biaya_trf'],2,".",","); ?>" >
                            </div>
                            <div class="col-md-6 form-group mt-3">
                                <label for="hp_penerima">No HP</label>
                                <input type="tel" name="hp_penerima" class="form-control" value="<?php echo $row['hp_penerima']; ?>" >

                                <input type="hidden" name="token" class="form-control "  value=" <?php echo$row['token']; ?>" >
                                <input type="hidden" name="id" class="form-control "  value=" <?php echo$id; ?>" >
                                <input type="hidden" name="st_antri" class="form-control "  value="3" >
                                <input type="hidden" name="jns_trx" class="form-control "  value="trf" >
                            </div>
                        </div>
                        <br>
                    </div>
              </div>
              
              <div class="my-3 mt-3">
              </div>
              <div class="row">
                  <div class="col-md-3 form-group mt-3">
                  </div>
                  <div class="col-md-2 form-group mt-3">
                      <div class="text-center"><button class="btn btn-success" type="submit"><h5>Save</h5></button></div>
                  </div>
                  <div class="col-md-2 form-group mt-3">
                      <div class="text-center"><a href="list.php?jenis=Teller" class="btn btn-warning" type="button"><h5>Kembali</h5></a></div>
                  </div>
				  <div class="col-md-2 form-group mt-3">
                      <div class="text-center"><a target="_blank" href="print_trf.php?iid=<?php echo$row['token']; ?>" class="btn btn-primary" type="button"><h5>Print</h5></a></div>
                  </div>
              </div>
          

          </div>
        </div>

      </div>
    </section><!-- End Contact Section -->


    <?php } ?>
    </form>

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
  <script src="assets/js/select2.js"></script>
  <?php include 'inputan.php';?>
</body>

</html>