<!DOCTYPE html>
<html lang="en">


<?php include 'head_adm.php';?>
<link rel="stylesheet" href="assets/w3.css">
<?php 
    require 'connection.php';
    $sql = "SELECT * from tbl_set where jenis_set='Video' ;"; 
    $result = $conn->query($sql);
    // output data of each row
    if($row = $result->fetch_assoc()) {
        $respon = $row['value'];
    }else{
        $respon = "No_media";
    }
?>
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

  

    <!-- ======= Pricing Section ======= -->
    <section id="pricing" class="pricing">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2> List Index Teller, CS & Admin</h2>
         </div>

        <div class="row">
          <div class="col-lg-3 col-md-6" data-aos="fade-down" data-aos-delay="100">
            <div class="box featured">
              <h3>Antrian CS</h3>
              <h4>
                <i style="color:rgb(255, 195, 0);" class="fa-solid fa-users fa-2x"></i>
              </h4>
              <div class="btn-wrap">
                <a href="list.php?jenis=CS" class="btn-buy">List CS</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" data-aos="fade-down" data-aos-delay="200">
            <div class="box featured">
              <h3>Antrian Teller</h3>
              <h4><i style="color:rgb(35, 172, 101);" class="fa-solid fa-money-bill-transfer fa-2x"></i></h4>
              <div class="btn-wrap">
                <a href="list.php?jenis=Teller" class="btn-buy">List Teller</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" data-aos="fade-down" data-aos-delay="300">
            <div class="box featured">
              <h3>Report Transaksi</h3>
              <h4><i style="color:rgb(36, 137, 192);" class="fa-solid fa-print fa-2x"></i></h4>
              <div class="btn-wrap">
                <a href="#" onclick="document.getElementById('id01').style.display='block'"  class="btn-buy">Cek Yuk</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" data-aos="fade-down" data-aos-delay="400">
            <div class="box featured">
              <h3>Tampilan Antrian</h3>
              <h4><i style="color:rgb(192, 36, 113);" class="fa-solid fa-display fa-2x"></i></h4>
              <div class="btn-wrap">
                <a href="tampil_antrian.php" class="btn-buy">Tampil Yuk</a>
              </div>
            </div>
          </div>


        


        </div>

        <div class="row">
          <div class="col-lg-3 col-md-6" >
            
          </div>  
          <div class="col-lg-3 col-md-6 mt-3" >
            <div class="box featured">
              <h3>Setting Transaksi</h3>
              <h4>
                <i style="color:rgb(35, 172, 101);" class="fa-solid fa-lock fa-2x"></i>
              </h4>
              <div class="btn-wrap">
                <a href="setting_view.php" class="btn-buy">Setting View</a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mt-3" >
            <div class="box featured">
              <h3>Upload Media</h3>
              <h4><i style="color:rgb(36, 137, 192);" class="fa-solid fa-folder-open fa-2x"></i></h4>
              <div class="btn-wrap">
                <a href="#" onclick="document.getElementById('id02').style.display='block'"  class="btn-buy">Cek Yuk</a>
              </div>
            </div>
          </div>

          

        </div>

      </div>
    </section><!-- End Pricing Section -->

   
   

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

  <div id="id01" class="w3-modal">
    <div class="w3-modal-content">
      <div class="w3-container">
          <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
          <form action="p_multi.php" method="post" role="form">
                  <div class="row">
                    <div class="col-md-4 form-group mt-3">
                        <label for="">Dari Tanggal </label>
                        <input type="date" class="form-control" name="tgl1" id="tgl1" required>
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <label for="no_rek">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="tgl2" id="tgl2" required>
                    </div>
                    <div class="col-md-3 form-group mt-3">
                      <br>
                      <button class="btn btn-success btn-xs" type="submit">Tarik Yuk</button>
                    </div>
                  </div>
                  <br>
          </form>
      </div>
    </div>
  </div>

  <div id="id02" class="w3-modal">
    <div class="w3-modal-content">
      <div class="w3-container">
          <span onclick="document.getElementById('id02').style.display='none'" class="w3-button w3-display-topright">&times;</span>
          <form action="save_media.php" method="post" role="form"  enctype='multipart/form-data'>
                  <div class="row">
                  <div class="col-md-6 form-group mt-3">
                        <label for="">Media Aktif </label>
                        <video  class="form-control" width="180" height="300" controls autoplay loop playsinline muted>
                            <source src="assets/media/<?php echo $respon; ?>">
                        </video>
                    </div>
                    <div class="col-md-6 form-group mt-3">
                        <label for="">Media Baru (Max 20MB)</label>
                        <input type="file" class="form-control" name="file" accept="video/*" required>
                        <br>
                      <button class="btn btn-success btn-xs" type="submit">Simpan Yuk</button>
                    </div>
                  </div>
                  <br>
          </form>
      </div>
    </div>
  </div>
     

  
</body>

</html>