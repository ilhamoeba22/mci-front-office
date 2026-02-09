<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
<?php include 'head_adm.php';?>

<body>
<?php 
     $id = $_GET['id'];
     require 'connection.php';
     $sql = "select * from tbl_set where id_set='$id';"; 
     $result = $conn->query($sql);
?>
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
          <h2>Edit Setting</h2>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
          <div class="col-lg-1"></div>
          <div class="col-lg-10" data-aos="fade-left" data-aos-delay="100">
              <form action="setting_editkeun.php" method="post" role="form">
              <?php while($row = $result->fetch_assoc()) { ?>
                    <div class="row">
                        <div class="col-md-6 form-group mt-3">
                            <label for="no_rek">Jenis Setting </label>
                            <input type="text" name="jenis" class="form-control numeric" id="jenis" required value="<?php echo $row['jenis_set']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mt-3">
                            <label for="nama">Nama Setting</label>
                            <input type="text" name="nama" class="form-control huruf" id="nama" required value="<?php echo $row['nama_set']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mt-3">
                            <label for="saldo">Value</label>
                            <input type="text" name="value" class="form-control huruf" id="value" required value="<?php echo $row['value']; ?>">
                        </div>
                    </div>
                <div class="my-3 mt-3">
                </div>
                <div class="text-center"><button class="btn btn-warning" type="submit"><h5>Simpan Yuk</h5></button></div>
                <?php } ?>
              </form>
          </div>
          <div class="col-lg-1"></div>
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