<!DOCTYPE html>
<html lang="en">

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
    <style>
        <?php include 'include/assets/css/search.css'; ?>
    </style>
    <?php include "presentation/categoryP.php";
    $cp = new categoryP;
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Chào đón năm mới, sale sập sàn 10-20%</a>
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
    <div class="container">
        <a href="http://localhost/web1/index.php"> <button style="color:darkgoldenrod
        ;font-size:30px">
                <p class="nhapnhay">Xem sản phẩm giảm giá sốc!</p>
            </button></a>
        <h3 class="flash" style="text-align:center;font-size:40px;color:cornflowerblue">TOP 3 SẢN PHẨM ĐƯỢC XEM NHIỀU NHẤT THÁNG </h3>
        <div class="row">
            <?php
            
            include "presentation/productP.php";
            $pp = new productP;
            
                $pp->ShowHotViews();

            ?>
        </div>

        <!-- <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item" style="padding: 2px;
                border: 5px solid white;"><a class="page-link" href="#">Previous</a></li>
                <?php
                //  $cp->ShowLinkPagination();
                ?>
                <li class="page-item" style="padding: 2px;
                border: 5px solid white;"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav> -->

    </div>
    <script>
        function searchFunction() {
            let x = document.getElementById('searchValue').value;
            let searchURL = "http://localhost/web1/search.php?s=" + x
            window.location.href = searchURL;
        }
    </script>
    <script src="include/assets/vendor/jquery/jquery.min.js"></script>
    <script src="include/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>