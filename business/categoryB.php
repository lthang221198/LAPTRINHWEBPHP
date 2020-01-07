<?php include "data/database.php"; ?>
<?php 
// $test = new CategoryB();
// echo $test->GetAmountOfProductInCategory(1);
// echo $test->CalculateNumberOfLinks(1);
class CategoryB{
    private $cat_list = null;
    private $MAX_PRODUCT = 3;
    public function GetAllCategories(){
        $sql = "SELECT * FROM category";
        $db = new database;
        $this->cat_list = $db->select($sql);
        return $this->cat_list;
    }
    public function GetAmountOfProductInCategory($category_id){
        $sql = "SELECT COUNT(*) as NUM FROM product WHERE cat_id = {$category_id}";
        $db = new database;
        $result = $db->select($sql);
        $row = mysqli_fetch_array($result);
        $num = $row['NUM'];
        return $num;
    }
    public function CalculateNumberOfLinks($category_id){
    
        $session_name="numPages_".$category_id;
       // echo var_dump($_SESSION["{$session_name}"]);
        // echo $session_name;
        if(isset($_SESSION["{$session_name}"]))
        {
            $result=$_SESSION["{$session_name}"];
            return $result;
        }
        $num = $this->GetAmountOfProductInCategory($category_id);
        $max = $this->MAX_PRODUCT;
        $result = (float) $num / $max;
        $result = ceil($result);
        $_SESSION["{$session_name}"]=$result;
        return $result;
    }
    public function GetProductsInGroup($category_id, $link_num){
        $offset = ($link_num-1) * $this->MAX_PRODUCT;
        $sql = "SELECT * FROM product WHERE cat_id={$category_id} LIMIT {$offset},{$this->MAX_PRODUCT}";
        $db = new database;
        $result = $db->select($sql);
        
        return $result;
    }
}
?>