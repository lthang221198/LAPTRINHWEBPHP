
<?php

$from="2019-01-01";
$to="2019-12-12";
    class InventoryB{
        public function GetGoodPerformanceList($from, $to){
            //1. Get product_id
            $product_list = $this->GetRelevantProductID($from, $to);
            //2. Update "return" array
            $plist = array();
           // echo $product_list;
            while ($row = mysqli_fetch_array($product_list)){
                $product_id = $row['product_id'];
                // 2.1 get correct performance
                $performance = $this->GetGoodPerformance($product_id);
                $plist["{$product_id}"] = "{$performance}";
            }
            asort($plist);
            //foreach($plist as $x=>$x_value) {echo $x . $x_value;}
            return $plist;
        }
        public function GetPoorPerformanceList($from, $to){
            //1. Get product_id
            $product_list = $this->GetRelevantProductID($from, $to);
            //2. Update "return" array
            $plist = array();
           // echo $product_list;
            while ($row = mysqli_fetch_array($product_list)){
                $product_id = $row['product_id'];
                // 2.1 get correct performance
                $performance = $this->GetLatestPerformance($product_id);
                $plist["{$product_id}"] = "{$performance}";
            }
            asort($plist);
            //foreach($plist as $x=>$x_value) {echo $x . $x_value;}
            return $plist;
        }
        public function GetLatestPerformance($product_id){
            // $sql = "SELECT performance FROM inventory_performance WHERE id = (
            //     SELECT max(id) FROM (SELECT * FROM inventory_performance WHERE product_id = {$product_id}) as Temp
            // )";
            $sql = "SELECT performance FROM inventory_performance WHERE product_id = {$product_id} ORDER BY ip_id DESC LIMIT 1";
            $db = new database;
            $result = $db->select($sql);
            $row = mysqli_fetch_array($result);
            return $row['performance'];
        }
        public function GetGoodPerformance($product_id){
            // $sql = "SELECT performance FROM inventory_performance WHERE id = (
            //     SELECT max(id) FROM (SELECT * FROM inventory_performance WHERE product_id = {$product_id}) as Temp
            // )";
            $sql = "SELECT performance FROM inventory_performance WHERE product_id = {$product_id} ORDER BY ip_id ASC LIMIT 1";
            $db = new database;
            $result = $db->select($sql);
            $row = mysqli_fetch_array($result);
            return $row['performance'];
        }
        public function GetRelevantProductID($from, $to){
            $FROM="'".$from."'";
            $TO="'".$to."'";
             $sql = "SELECT DISTINCT product_id FROM inventory_performance 
             WHERE from_date > $FROM AND to_date < $TO";
            //$sql="SELECT * from inventory_performance";
            $db = new database;
            $result = $db->select($sql);
            return $result;
        }
        public function UpdatePerformanceTable($product_id, $from, $to){
            $performance = $this->CalculatePerformance($product_id, $from, $to);
            $sql = "INSERT INTO inventory_performance (product_id, from_date, to_date, performance) VALUES ({$product_id}, '{$from}', '{$to}', {$performance})";
            $db = new database;
            $result = $db->insert($sql);
        }
        public function CalculatePerformance($product_id, $from, $to)
        {
            //1. Get all relevate inventory_id
            $list = $this->GetRelevantInventoryID($product_id, $to);
            //2. Sum M_S, M_I => P_ID
            $sum_M_S = 0;
            $sum_M_I = 0;
            while ($row = mysqli_fetch_array($list)){
                $inventory_id = $row['inventory_id'];
                $import_date = $row['import_date'];
                //2.1 Find out M_S
                $sum_M_S += $this->MarkOfSoldItems($inventory_id, $from, $to, $import_date);
                //2.2 Find out M_I
                $sum_M_I += $this->MarkOfInStockItems($inventory_id, $import_date);
            }
            return $sum_M_S/($sum_M_S + $sum_M_I);
        }
        public function MarkOfInStockItems($inventory_id, $import_date){
            //1. Get latest record
            $record = $this->GetLatestInventory($inventory_id);
            //2. Calculate 1 row
            $row = mysqli_fetch_array($record);
            $in_stock_amout = $row['in_stock'];
            $M = strtotime($import_date);
            $M_I = $M * $in_stock_amout;
            return $M_I;
        }
        public function GetLatestInventory($inventory_id){
            $sql = "SELECT * FROM inventory_management WHERE inventory_id = {$inventory_id} ORDER BY update_date DESC LIMIT 1";
            $db = new database;
            $result = $db->select($sql);
            return $result;
        }
        public function MarkOfSoldItems($inventory_id, $from, $to, $import_date)
        {
            // 1. Get correct id
            $list = $this->GetCorrectSoldItems($inventory_id, $from, $to);
            // 2. Calculate row by row
            $total = 0;
            $M = strtotime($import_date);
            while ($row = mysqli_fetch_array($list)){
                $export_date = $row['export_date'];
                $E = strtotime($export_date);
                $N = strtotime($to);
                $M_S = $N - ($E-$M);
                $total += $M_S;
            }
            return $total;
        }
        public function GetCorrectSoldItems($inventory_id, $from, $to){
            $sql = "SELECT * FROM inventory_out WHERE inventory_id = {$inventory_id} AND export_date > '{$from}' AND export_date < '{$to}'";
            $db = new database;
            $result = $db->select($sql);
            return $result;
        }
        public function GetRelevantInventoryID($product_id, $to)
        {
            $sql = "SELECT * FROM inventory_in WHERE product_id = {$product_id}
             AND import_date< '{$to}'";
            $db = new database;
            $result = $db->select($sql);
            return $result;
        }
    }
     function TestGetRelevantInventoryID(){
        $test = new InventoryB();
        $from = "2019-08-01";
        $to = "2019-09-05";
        $result = $test->GetRelevantInventoryID(1, $to);
        while ($row = mysqli_fetch_array($result)){
            echo $row['product_id'];
        }
    }
    function TestGetCorrectSoldItems(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->GetCorrectSoldItems(1, $from, $to);
        while ($row = mysqli_fetch_array($result)){
            echo $row['export_date'];
        }
    }
    function TestMarkOfSoldItems(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->MarkOfSoldItems(1, $from, $to, "2019-09-03");
        echo $result;
    }
    function TestGetLatestInventory(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->GetLatestInventory(1);
        while ($row = mysqli_fetch_array($result)){
            echo $row['product_id'];
        }
    }
    function TestMarkOfInStockItems(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->MarkOfInStockItems(1, "2019-09-03");
        echo  $result;
    }
    function TestCalculatePerformance(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->CalculatePerformance(1, $from, $to);
        echo  $result;
    }
    function TestUpdatePerformanceTable(){
        $test = new InventoryB();
        $from = "2019-09-01";
        $to = "2019-09-05";
        $result = $test->UpdatePerformanceTable(1, $from, $to);
        // $result = $test->UpdatePerformanceTable(2, $from, $to);
    }
    function TestGetRelevantProductID(){
        $test = new InventoryB();
        $from = "2019-08-01";
        $to = "2019-11-05";
        $result = $test->GetRelevantProductID($from, $to);
        while ($row = mysqli_fetch_array($result)){
            echo $row['product_id']."</br>";
        }
    }
    function TestGetLatestPerformance(){
        $test = new InventoryB();
        $from = "2019-08-01";
        $to = "2019-10-05";
        $result = $test->GetLatestPerformance(2);
        echo $result;
    }
    function TestGetPoorPerformanceList(){
        $test = new InventoryB();
        $from = "2019-08-01";
        $to = "2019-11-05";
        $result = $test->GetPoorPerformanceList($from, $to);
        asort($result);
        foreach($result as $key => $value) {
            echo $key."-".$value;
            echo "</br>";
        }
    }
?>