<!DOCTYPE html>
<html lang="en">
<script src="assets/js/jquery-3.5.0.js"></script>
<?php $id_jenis = $_GET['jenis']; 
if($id_jenis == "CS"){
    $jenis = "Customer Service";
}else{
    $jenis = "Teller";
}
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

  

     <!-- ======= Contact Section ======= -->
     <section id="contact" class="contact">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>List Transaksi <?php echo $jenis ?></h2>
        </div>

        <div class="row d-flex justify-content-end" data-aos="fade-right" data-aos-delay="100">
            <div class="col-lg-10" data-aos="fade-left" data-aos-delay="100">
            <div class="table-responsive">
                    <table id="dtBasicExample" class="table">
                      <thead class="text">
                          <th>Antrian</th>
                          <th>Nama</th>
                          <th>Tanggal</th>
                          <th>Kode_Trans</th>
                          <th>Detail</th>
                          <th>Status</th>
                          <th>Panggil</th>
                      </thead>
                      <tbody>
                        <?php 
                          require 'connection.php';
                          if($id_jenis == "CS"){
                            $sql = "SELECT * from tbl_antrian where type='CS'order by id_antrian DESC;"; 
                          }else{
                            $sql = "SELECT * from tbl_antrian where type='Teller' order by id_antrian DESC;";  
                          }
                          $result = $conn->query($sql);
                          // output data of each row
                          while($row = $result->fetch_assoc()) {
                            
                          ?>
                        <tr>    
                          <td><?php echo $row['antrian'] ?></td>
                          <td><?php echo $row['nama_antrian'] ?></td>
                          <td><?php echo $row['tgl_antri'] ?></td>   
                          <td><?php echo $row['kode'] ?></td>       
                          <td><a href="list_all.php?id=<?php echo $row['kode'];?>">Detail</a></td> 
                          <?php if($row['st_antrian'] == 0){ ?>
                            <td style="background-color:#1e81b0; color:#eeeee4; ">Menuggu</td> 
                          <?php }elseif($row['st_antrian'] == 2){ ?>
                            <td style="background-color:#FFC300 ; ">Proses</td>  
                          <?php }else{ ?>
                            <td style="background-color:#24C0A1; ">Selesai</td>  
                          <?php } ?>
                          <?php if($row['st_antrian'] == 0){ ?>
                            <td style=""><a href="list_panggil.php?id=<?php echo $row['id_antrian'];?>&jns=<?php echo $row['type'];?>">Panggil</a></td> 
                          <?php }elseif($row['st_antrian'] == 2){ ?>
                            <td style="">Sedang Transaksi</td>  
                          <?php }else{ ?>
                            <td style=""></td>  
                          <?php } ?>

                        </tr>
                        <?php } ?>                  
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