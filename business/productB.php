<?php 
class ProductB{
    public function IncreaseView($product_id){
        $sql = "UPDATE product SET view = view + 1 WHERE product_id = {$product_id}";
        $db = new database;
        $result = $db->update($sql);
    }
    public function GetProduct($product_id){
        $sql = "SELECT * FROM product WHERE product_id = {$product_id}";
        $db = new database;
        $result = $db->select($sql);
        return $result;
    }
    public function GetProductInCategory($category_id){
    $sql = "SELECT * FROM product WHERE cat_id = {$category_id}";
        $db = new database;
        $result = $db->select($sql);
        return $result;
    }
    public function GetProductInCategory2($category_id){
        $sql = "SELECT t4.*, t3.performance
        FROM product as t4
        INNER JOIN
        (SELECT t1.*
        FROM inventory_performance as t1
        INNER JOIN
        (
            SELECT product_id, MAX(id) AS latest_id
            FROM inventory_performance
            GROUP BY product_id
        )as t2
            ON t1.product_id = t2.product_id AND t1.id = t2.latest_id) as t3 ON t3.product_id = t4.id
        WHERE t4.cat_id = {$category_id}";
            $db = new database;
            $result = $db->select($sql);
            return $result;
        }
        public function Search($query)
        {
        $sql = "SELECT * FROM product WHERE product_name LIKE '%$query%'";
        $db = new database;
        $result = $db->select($sql);
        return $result;
        }
        public function GetAmountofProductInSearch($query)
        {
            $sql = "SELECT count * FROM product WHERE product_name LIKE '%$query%'";
            $db = new database;
            $result = $db->select($sql);
            $row = mysqli_fetch_array($result);
            $num = $row['NUM'];
            return $num;
        }
        public function FindHotProduct()
        {
            $sql="SELECT v.*
            FROM product AS v
            INNER JOIN
                 (SELECT t1.product_id from product t1,view_current t2 WHERE t1.product_id=t2.product_id order by views_from_to desc limit 3) as v2
              ON v.product_id = v2.product_id
            ";
            $db= new Database;
            $result = $db->select($sql);
            return $result;
        }
}
?>