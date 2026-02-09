<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>

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

  

     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">
		<?php 
        	$type = explode("-",$_GET['id']);
           
        	if($type[0] == "'CS"){
              $pp = " CS";
            }else{ $pp ="Teller"; }
        ?>
        <div class="section-title">
          <h2>Kode Transaksi Anda</h2>
          <h6>Silakan ke <?php echo $pp; ?> Kami dan berikan kode anda</h6>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
            <div class="col-lg-2"></div>
            <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
                <center>
                   <label for=""> Kode Anda Adalah : </label><h1> <?php echo $_GET['id']; ?></h1>
                </center>
            </div>
            <div class="col-lg-2"></div>
        </div>
        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <form action="r_nas.php" method="post" role="form" target="_blank">
                  <div class="row">
                      <center>
                          <input type="hidden" class="form-control" readonly="" name="id" id="id" value="<?php echo $_GET['id']; ?>" required>
                          <div class="my-3 mt-3">
                          </div>
                          <button class="btn btn-warning" type="submit"><h5>Print</h5></button>
                      </center>
                  </div>
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

</body>

</html>