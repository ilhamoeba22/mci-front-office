<!DOCTYPE html>
<html lang="en">


<?php include 'head_adm.php';?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
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

      <h1 class="logo"><a href="aiueo.php"> BPRS H I K M C I</a></h1>
      <!-- Uncomment below if you prefer to use an image logo -->
      <!-- <a href=index.html" class="logo"><img src="assets/img/logo.png" alt="" class="img-fluid"></a>-->

    

    </div>
  </header><!-- End Header -->
  
  <main id="main">
    <!-- ======= Pricing Section ======= -->
    <section id="pricing" class="pricing">
      <div class="container" data-aos="fade-up">
       
        <div class="row">
            <div class="col-lg-4" data-aos="fade-down" data-aos-delay="100">
                <div class="col-lg-12" data-aos="fade-down" data-aos-delay="100">
                  <br><br>
                    <div class="box featured" >
						<h3 style="font-size:45px;" >Antrian CS</h3>
						<h4 style="font-size:80px; color: #232F39" name="cs" id="cs"></h4>
						<div class="btn-wrap">
						</div>
                    </div>
                </div>
                <div class="col-lg-12" data-aos="fade-down" data-aos-delay="200">
                    <div class="box featured">
						<h3 style="font-size:45px;">Antrian Teller</h3>
						<h4 style="font-size:80px; color: #232F39" name="tl" id="tl"></h4>
						<div class="btn-wrap">
						</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8" data-aos="fade-down" data-aos-delay="100">
              <video width="100%" height="570" controls autoplay loop playsinline muted>
                  <source src="assets/media/<?php echo $respon; ?>">
              </video>
            </div>
			<div class="col-lg-12" >
                <marquee behavior="scroll" direction="left"><h4 style="font-size:52px;">
					PENAWARAN KHUSUS BULAN INI 
					NISBAH DEPOSITO 1 BULAN SEBESAR 35.00% | NISBAH DEPOSITO 3 BULAN SEBESAR 43.00% |           
					NISBAH DEPOSITO 6 BULAN SEBESAR 43.00% | NISBAH DEPOSITO 12 BULAN SEBESAR 50.00%
				</h4></marquee>

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
<script>
  $('document').ready(function () {
    setInterval(function () { getData()}, 2000); // panggil setiap 2 detik
  }); 

  function getData() {
      $.ajax({
          url: "get_antrian.php",
          type: "GET",                
          success: function(response){ 
            var jsonData = JSON.parse(response);  
            document.getElementById('cs').innerHTML = jsonData.cs_antri;
            document.getElementById('tl').innerHTML = jsonData.tl_antri;
      }
    });
  }

</script>

</body>

</html>