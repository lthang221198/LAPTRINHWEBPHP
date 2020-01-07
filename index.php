<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<style>
  <?php include 'include/assets/css/index.css'; ?>
</style>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>LongThang Homepage - Welcome to my Website</title>

  <!-- Bootstrap core CSS -->
  <link href="include/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="include/assets/css/shop-homepage.css" rel="stylesheet">

</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">Welcome to my Website</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>
          <div class="form-inline my-2 my-lg-0">
            <input id="searchValue" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button onclick="searchFunction()" class="btn btn-outline-success my-2 my-sm-0">Search</button>
          </div>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">

    <div class="row">

      <div class="col-lg-3">

        <h1 class="my-4" class="flash" style="color:#2f3640;
        font-size:30px;font-family:Georgia, 'Times New Roman', Times, serif;text-align:center">TDTL GROUP UIT Mobile88 Shop</h1>
        <!-- <div class="list-group" style="border-radius:5px;padding: 15px;
        border: 5px solid green;">          
        </div> -->
        <div class="card">
          <div class="card-header">
            <h5 style="color: #2f3640">Danh mục sản phẩm</h5>
          </div>
          <ul class="list-group list-group-flush">
            <?php include "presentation/categoryP.php";
            $cp = new categoryP;
            $cp->ShowAllCategories();
            ?>
          </ul>
        </div>
        <div class="card" style="margin-top: 15px">
          <a href="http://localhost/web1/bestviewproducts.php"><button style="width:100%;height:60px;" class="flash">
              Sản phẩm được xem nhiều nhất trong Quý
            </button>
          </a>
        </div>
      </div>
      <!-- /.col-lg-3 -->

      <div class="col-lg-9">

        <div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel" data-interval="1000">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
              <img class="d-block img-fluid " style="width:100%;height:270px" src="https://i.ytimg.com/vi/NRWT37t2LDM/maxresdefault.jpg" alt="First slide">
            </div>
            <div class="carousel-item">
              <img class="d-block img-fluid" style="width:100%;height:270px" src="https://cdn.tgdd.vn/Files/2016/02/03/782021/tgdd-khai-truong-cong-quynh-800-300.jpg" alt="Second slide">
            </div>
            <div class="carousel-item">
              <img class="d-block img-fluid" style="width:100%;height:270px" src="http://www.theautohost.com/_contentPages/vehicleContentPages/hyundai/2017/tuscon/images/header.jpg" alt="Third slide">
            </div>
            <!-- add slide -->
          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>

        </div>

        <div class="row">
          <?php include "presentation/productP.php";
          $pp = new productP;
          $pp->ShowProductsByUser();
          // $result = searchFunction();
          // $pp->ShowSearch($result);
          ?>


        </div>
        <nav aria-label="Page navigation example">
          <ul class="pagination">
            <li class="page-item" style="cursor: pointer">
              <a class="page-link" onclick="previousGroupProduct()">&laquo; Previous</a>
            </li>
            <?php
            $cp->ShowLinkPagination();
            ?>
            <li class="page-item" style="cursor: pointer">
              <a class="page-link" onclick="nextGroupProduct()">Next &raquo;</a>
            </li>
          </ul>
        </nav>
        <!-- /.row -->

      </div>
      <!-- /.col-lg-9 -->

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->

  <!-- Footer -->
  <footer class="py-5 bg-dark">
    <div class="container">
      <p class="m-0 text-center text-white">Copyright &copy; Website E-Commerce 2019</p>
    </div>
    <!-- /.container -->
  </footer>
  <script>
    function searchFunction() {
      let x = document.getElementById('searchValue').value;
      let searchURL = "http://localhost/web1/search.php?s=" + x
      window.location.href = searchURL;
    }
  </script>
  <script>
    function previousGroupProduct() {
      let url = window.location.href;
      console.log(url);

      let str = "product_group=";
      let n = url.indexOf(str);
      let groupNumber = url.substring(n + str.length, url.length);
      if (groupNumber > 1) {
        groupNumber--;
        let newURL = url.substring(0, n) + str + groupNumber;
        window.location.href = newURL;
      };
    }

    function nextGroupProduct() {
      let maxGroupNumber = document.getElementById("maxGroupNumber").attributes[1].value;
      let url = window.location.href;
      console.log(url);

      let str = "product_group=";
      let n = url.indexOf(str);
      let groupNumber = url.substring(n + str.length, url.length);
      if (groupNumber < maxGroupNumber) {
        groupNumber++;
        let newURL = url.substring(0, n) + str + groupNumber;
        window.location.href = newURL;
      };
    }
  </script>
  <!-- Bootstrap core JavaScript -->
  <script src="include/assets/vendor/jquery/jquery.min.js"></script>
  <script src="include/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

</body>

</html>