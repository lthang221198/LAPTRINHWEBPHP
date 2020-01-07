<?php include "business/productB.php"; ?>
<?php include "business/inventoryB.php"; ?>
<?php include "business/productAnalysisB.php"; ?>
<?php 
 //ham lam tron
  function floorp($val, $precision)
 { 
     $mult = pow(10, $precision); // Can be cached in lookup table        
     return floor($val * $mult) / $mult;
 }
class ProductP{
    private $from = "2019-01-01";
    private $to = "2019-12-31";
    public function GetIdProductCurrent(){
        $product_id = 0;
        if (isset($_GET['id'])){
            $product_id = $_GET['id'];
        }
        return $product_id;
    }
    public function ShowProductsByUser(){
        $cp = new CategoryP();
        $category_id = $cp->GetCategory();
        $product_group = $cp->GetGroup();
        if($category_id == 0)
        {
            $this->ShowFeaturedProduct();
        }
        else{
            $this->ShowProductInGroup($category_id, $product_group);
        }
    }
    public function ShowSingleProduct($product)
    {
        $item = <<<DELIMITER
                    <div class="col-lg-12 col-md-12 mb-12">
                    <div class="card h-100">
                    <a href="item.php?id={$product['product_id']}"><img class="card-img-top" src="https://cdn.tgdd.vn/Products/Images/42/210655/iphone-11-pro-256gb-tgdd28.jpg" alt=""></a>
                    <div class="card-body">
                        <h4 class="card-title">
                        <a href="item.php?id={$product['product_id']}">{$product['product_name']}</a>
                        </h4>
                        <h5>\${$product['product_price']}</h5>
                        <p class="card-text">Good product from USA</p>
                        <a href="paypal.html"><button type="button" class="btn btn-success">Add to Cart!!</button></a>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                    </div>
                    </div>
                </div>
            DELIMITER;
            echo $item;
    }
    //Show Single Product is not good
    public function ShowSingleProductNotGood($product,$new_price)
    {
      
        $item = <<<DELIMITER
                    <div class="col-lg-12 col-md-12 mb-12">
                    <div class="card h-100">
                    <a href="item.php?id={$product['product_id']}"><img class="card-img-top" src="https://cdn.tgdd.vn/Products/Images/42/210655/iphone-11-pro-256gb-tgdd28.jpg" alt=""></a>
                    <div class="card-body">
                        <h4 class="card-title">
                        <a href="item.php?id={$product['product_id']}">{$product['product_name']}</a>
                        </h4>
                        <strong style="text-decoration: line-through;">\${$product['product_price']}</strong>
                        <span style="color:red;margin-left:5px;font-style:bold">\$$new_price</span>
                        <label style="font-style: italic;color:orange;margin-left:15px;background-color:yellow
                        ;border-radius:10px"> Trả góp 0% </label>
                        <p class="card-text">Good product from USA</p>
                        <a href="paypal.html"><button type="button" class="btn btn-success">Add to Cart!!</button></a>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                    </div>
                    </div>
                </div>
            DELIMITER;
            echo $item;
    }
    public function ShowProduct($product)
    {
        $item = <<<DELIMITER
                    <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                    <a href="item.php?id={$product['product_id']}"><img class="card-img-top" src="https://cf.shopee.vn/file/8a71c338e447f0ead495427f20196a73" alt="iphone X"></a>
                    <div class="card-body">
                        <h4 class="card-title">
                        <a href="item.php?id={$product['product_id']}">{$product['product_name']}</a>
                        </h4>
                        <h5>\${$product['product_price']}</h5>
                        <p class="card-text">This is a good phone for you on this Promotion!</p>
                        <a href="paypal.html"><button type="button" class="btn btn-success">Add to Cart!!</button></a>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                    </div>
                    </div>
                </div>
            DELIMITER;
            echo $item;
    }
    public function ShowProductNotGood($product,$new_price)
    {   
        $item = <<<DELIMITER
                    <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                    <a href="item_not_good.php?id={$product['product_id']}"><img class="card-img-top" src="https://cf.shopee.vn/file/8a71c338e447f0ead495427f20196a73" alt="iphone X"></a>
                    <div class="card-body">
                        <h4 class="card-title">
                        <a  href="item_not_good.php?id={$product['product_id']}">{$product['product_name']}</a>
                        </h4>
                        <strong style="text-decoration: line-through;">\${$product['product_price']}</strong>
                        <span style="color:red;margin-left:5px;font-style:bold">\$$new_price</span>
                        <label style="font-style: italic;color:orange;margin-left:15px;background-color:yellow
                        ;border-radius:10px"> Trả góp 0% </label>
                        <p class="card-text">This is a good phone for you on this Promotion!</p>
                        <a href="paypal.html"><button type="button" class="btn btn-success">Add to Cart!!</button></a>
                    </div>
                    <div class="card-footer" >
                        <small class="text-muted" style="color:green !important">&#9733; &#9733; &#9733; &#9733; &#9734;</small>
                    </div>
                    </div>
                </div>
            DELIMITER;
            echo $item;
    }
   
  //function Search
  public function SearchTool()
  {
  $in=new productB();
  }
    public function ShowFeaturedProduct(){
        $from = "2019-09-30";
        $to = "2019-12-05";
        $product_name="iphone x 64gb";//lay ra san pham di cao gia
        //1. Get product list sorted by performance
        $ib = new InventoryB();
        $featuredList = $ib->GetPoorPerformanceList($from, $to);
        echo  "<div style='width: 100%; padding-left: 15px;'><h3>Best sale</h3></div>";
        foreach($featuredList as $x => $x_value)
        {
            $pb = new ProductB();
            $result = $pb->GetProduct($x);
            $row = mysqli_fetch_array($result);
            $update_price=new ProductAnalysisB();//khoi tao object
            $price=$update_price->SearchCompetitivePrice($product_name);//lay gia min
            $new_price=floorp($price*0.9,2);
            //$new_price=floorp($row['product_price']*0.9,2);
            $this->ShowProductNotGood($row,$new_price);
        }
    }
    public function ShowProductInCategory(){
        $pb = new ProductB();
        $cp = new CategoryP();
        $category_id = $cp->GetCategory();
        $result = $pb->GetProductInCategory($category_id);
        // $result = $pb->GetProductInCategory2($category_id);
        if ($result == null) {
            echo "<div style='text-align: center; width: 100%;'>No Product Here!</div>";
            return;
        }
        while($row = mysqli_fetch_array($result))
        {
            $this->ShowProduct($row['product_name'],$row['product_price'],$row['product_id']);
        }
    }
    public function ShowProductItem(){
        //$from="2019-01-01";
        //$to="2019-12-31";
        $pb = new ProductB();
        $pab = new ProductAnalysisB();
        $product_id = $this->GetIdProductCurrent();

        $result = $pb->GetProduct($product_id);
        if ($result == null) {
            echo "<div style='text-align: center; width: 100%;'>No Product Here!</div>";
            return;
        }
        $row = mysqli_fetch_array($result);
        $pb->IncreaseView($product_id);//tang view cau 2
        $this->ShowSingleProduct($row);
        $pab->UpdateViewOfProduct($product_id);
        $pab->UpdateViewToTable($product_id,$this->from,$this->to);//tang view theo kieu duyet ngay
    }
    //Show products are not good
    public function ShowProductItemNotGood(){
        $pb = new ProductB();
        $pab = new ProductAnalysisB();
        $from="2019-01-01";
        $to="2019-12-31";
        $product_id = $this->GetIdProductCurrent();
        $product_name="iphone x 64gb";
        $result = $pb->GetProduct($product_id);
        if ($result == null) {
            echo "<div style='text-align: center; width: 100%;'>No Product Here!</div>";
            return;
        }
        $row = mysqli_fetch_array($result);
        //$new_price=floorp($row['product_price']*0.9,2);
        $price=$pab->SearchCompetitivePrice($product_name);//lay gia min sau khi cao gia
        $new_price=floorp($price*0.9,2);
        $this->ShowSingleProductNotGood($row,$new_price);
        $pb->IncreaseView($product_id);//tang view cau 2
        $pab->UpdateViewOfProduct($product_id);
        $pab->UpdateViewToTable($product_id,$this->from,$this->to);//tang view theo kieu duyet ngay
    }
    //Ham add session List of products
    public function SessionVarForProductName($category_id,$product_group,$product_name,$count)
    {
        $session_name=$category_id."_".$product_group."_"."name"."_".$count;
        
            $_SESSION["{$session_name}"]=$product_name;            
    }
    public function SessionVarForProductGroup($category_id,$product_group,$product_name,$count)
    {
        $session_name=$category_id."_"."product_group"."_".$product_name."_".$count;
        
            $_SESSION["{$session_name}"]=$product_group;            
    }
    public function SessionVarForProductId($category_id,$product_group,$product_name,$count)
    {
        $session_name="category_id"."_".$product_group."_".$product_name."_".$count;
        
            $_SESSION["{$session_name}"]=$category_id;            
    }

    public function ShowProductInGroup($category_id, $product_group){
        $cb = new CategoryB();
        $result = $cb->GetProductsInGroup($category_id, $product_group);
        while($row = mysqli_fetch_array($result))
        {
            $this->ShowProduct($row);
        }
    }
    public function ShowSearch($query)
    {
        $cb= new productB();
        $result=$cb->Search($query);  
        while($row = mysqli_fetch_array($result))
        {
            $this->ShowProduct($row);
        }
    }
    public function ShowHotViews()
    {
        $cb=new productB();
        $result=$cb->FindHotProduct();
        while($row = mysqli_fetch_array($result))
        {
            $this->ShowProduct($row);
        }
    }
}
?>