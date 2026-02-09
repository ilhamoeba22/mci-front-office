<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
<?php 
     $id = $_GET['id'];
     require 'connection.php';
     $sql = "select * from tbl_antrian where id_antrian='$id';"; 
     $result = $conn->query($sql);
?>

<?php include 'head.php';?>

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

  

     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Antrian CS</h2>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
            <div class="col-lg-2"></div>
          <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
            <form action="list_done.php" method="post" role="form">
                <?php while($row = $result->fetch_assoc()) { ?>
                    <div class="row">
                        <div class="col-md-4 form-group mt-3">
                            <label for="">hari, Tanggal</label>
                            <input type="text" class="form-control" readonly name="tgl" id="tgl" value="<?php echo $row['tgl_antri']; ?>">
                        </div>
                        <div class="col-md-4 form-group mt-3">
                            <label for="nama">Type Antrian</label>
                            <input type="text" name="nama" class="form-control huruf" id="nama" value="<?php echo $row['type']; ?>" readonly>
                        </div>
                        <div class="col-md-4 form-group mt-3">
                            <label for="no_rek">Kode Transaksi</label>
                            <input type="text" name="no_rek" class="form-control numeric" id="no_rek" value="<?php echo $row['kode']; ?>" readonly>
                        </div>
                        <div class="col-md-6 form-group mt-3">
                            <label for="tujuan_datang">Tujuan Datang </label>
                            <textarea name="tujuan_datang" class="form-control kalimat" id="" cols="30" rows="10"> <?php echo $row['tujuan_datang']; ?> </textarea>
                        </div>
                        <div class="col-md-6 form-group mt-3">
                            <label for="solusi">Solusi / Tanggapan</label>
                            <textarea name="solusi" class="form-control kalimat" id="" cols="30" rows="10"> <?php echo $row['solusi']; ?> </textarea>
                            <input type="hidden" name="id_antrian" class="form-control "  value=" <?php echo $row['id_antrian']; ?>" >
                            <input type="hidden" name="st_antri" class="form-control "  value="3" >
                            <input type="hidden" name="jns_trx" class="form-control "  value="cs" >
                        </div>
                    </div>
                    
                    <div class="my-3 mt-3">
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group mt-3">
                        </div>
                        <div class="col-md-2 form-group mt-3">
                            <div class="text-center"><button class="btn btn-success" type="submit"><h5>Up & Done</h5></button></div>
                        </div>
                        <div class="col-md-2 form-group mt-3">
                            <div class="text-center"><a href="list.php?jenis=CS" class="btn btn-warning" type="button"><h5>Kembali</h5></a></div>
                        </div>
                    </div>
                <?php } ?>
            </form>
          </div>
          <div class="col-lg-2"></div>
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