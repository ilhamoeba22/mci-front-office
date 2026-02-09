<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
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

  

     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>List Setting Transaksi</h2>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
            <div class="col-lg-10" data-aos="fade-left" data-aos-delay="100">
            <div class="table-responsive">
                    <table id="dtBasicExample" class="table">
                      <thead class="text">
                          <th>No</th>
                          <th>Type</th>
                          <th>Nama Setting</th>
                          <th>Value</th>
                          <th>Status</th>
                      </thead>
                      <tbody>
                        <?php 
                          require 'connection.php';
                          $sql = "SELECT * from tbl_set where jenis_set != 'Video';"; 
                          $result = $conn->query($sql);
                          // output data of each row
                          while($row = $result->fetch_assoc()) {
                            $no=1;
                          ?>
                        <tr>  
                          <td><?php echo $no; ?></td>  
                          <td><?php echo $row['jenis_set'] ?></td>
                          <td><?php echo $row['nama_set'] ?></td>   
                          <td><?php echo number_format($row['value'],2,",","."); ?></td>       
                          <td><a href="setting_edit.php?id=<?php echo $row['id_set'];?>">Edit</a></td>                        
                        </tr>
                        <?php  $no++; } ?>                  
                      </tbody>
                    </table>
                  </div>
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
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script>
        // Basic example
        $(document).ready(function () {
        $('#dtBasicExample').DataTable({
            "pagingType": "simple", // "simple" option for 'Previous' and 'Next' buttons only
            "ordering" :false
        });
        $('.dataTables_length').addClass('bs-select');
        });
    </script>

</body>

</html>