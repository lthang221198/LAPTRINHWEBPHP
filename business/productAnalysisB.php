
<?php include "include/lib/simple_html_dom.php"; ?>
<?php 
// $from = "2019-08-01";
// $to = "2019-10-31";
 //$product_name = "samsung galaxy a30s";
 //$test = new ProductAnalysisB();
// // $return_list = $test->GetRelevantLinks($product_name);
// // $test->BuildUpDataset($product_name, $return_list);
// // $link = "https://fptshop.com.vn/dien-thoai/iphone-x";
// // $type = "class";
// // $rule = "price";
// // $test->CheckRuleMatchLink($link, $type, $rule);
// // $raw = "19.990.000₫ Trả góp 0%";
//$product_name='samsung galaxy a30s';
 //$test->SearchCompetitivePrice($product_name);
 //$test->GetMinPrice($product_name);
//$test->SearchCompetitivePrice("samsung galaxy a30s");
class ProductAnalysisB{
    private $high_view = 2;
    private $google_link = "https://www.google.com/search?q=";

    public function GetRelevantLinks($product_name){
        //1. Build search string
        $search = $this->BuildSearchString($product_name);
        $url = $this->google_link . $search;
        //2. Send search string and get result
        $html = file_get_html($url);
        //3. Analyze search result and get links
        $return_list = array();
        foreach($html->find('a') as $element){
            // Check if product_name exist in plaintext link
            $pos = stripos($element->plaintext, $product_name);
            if ($pos !== false){
                $link = $this->StandarizeLink($element->href);
                if ($link != -1) 
                    $return_list["{$element->plaintext}"] = "{$link}";
            }
        }
        return $return_list;
    }
    public function CompareClassRule($element, $rule, $link)
    {
        $class = 'Class: ' . $element->class.'<br>';
        if (stripos($class,$rule) !== false){
            $check_price = $this->GetPrice($element->plaintext);
            $flag = $this->CheckPrice($check_price);
            if ($flag == 1){
                $this->UpdatePriceInDataset($link, $check_price);
                return $check_price;
            }
            return 1;
        }
        return 0;
    }
    public function CompareIdRule($element, $rule, $link)
    {
        $id = 'ID: ' . $element->id.'<br>';
        if (stripos($id,$rule) !== false){
            $check_price = $this->GetPrice($element->plaintext);
            $flag = $this->CheckPrice($check_price);
            if ($flag == 1){
                $this->UpdatePriceInDataset($link, $check_price);
                return $check_price;
            }
            return 1;
        }
        return 0;
    }
    public function UpdatePriceInDataset($link, $price){
        $sql = "UPDATE `dataset` SET `price`  = {$price} WHERE `link_name` = '{$link}'";
        $db = new database;
        $result = $db->update($sql);
    }
    public function GetAllLinks($product_name)
    {
        $sql = "SELECT * FROM `dataset` WHERE product_name = '{$product_name}'";
        $db = new database;
        $result = $db->select($sql);
        $return_list = array();
        while ($row = mysqli_fetch_array($result))
        {
            $return_list["{$row['id']}"] = "{$row['link_name']}";
        }
        return $return_list;
    }
    public function GetAllRules()
    {
        $sql = "SELECT * FROM `rules`";
        $db = new database;
        $result = $db->select($sql);
        $return_list = array();
        while ($row = mysqli_fetch_array($result))
        {
            $return_list["{$row['id']}"] = "{$row['name']}";
        }
        return $return_list;
    }
    public function CheckRuleMatchLink($link, $type, $rule){
        $html = file_get_html($link);
        if ($html == false) return 0;
        $all = $html->find("*");
        $matched_num = 0;
        for ($i=0, $max=count($all); $i < $max; $i++) {
            if ($type == "class"){
                $matched_num += $this->CompareClassRule($all[$i], $rule, $link);
            } else if ($type == "id"){
                $matched_num += $this->CompareIdRule($all[$i], $rule, $link);
            } else if ($type == "any"){
                $matched_num += $this->CompareClassRule($all[$i], $rule, $link);
                $matched_num += $this->CompareIdRule($all[$i], $rule, $link);
            }
        }
        return $matched_num;
    }
    public function TrainRule($product_name){
        //1. Get dataset
        $return_list = $this->GetAllLinks($product_name);
        //2. Get rules and train
        $sql = "SELECT * FROM `rules`";
        $db = new database;
        $result = $db->select($sql);
        while ($row = mysqli_fetch_array($result))
        {
            $count = 0;
            foreach($return_list as $x => $x_value){
                $num = $this->CheckRuleMatchLink($x_value, $row['class_or_id'], $row['name']);
                $isLearnd = $this->IsLearnd($row['id'],$x);
                if ($num > 0) $count ++;
                if ($isLearnd == 0) {
                    if ($num > 0) {
                        $this->UpdateRelationshipTable($row['id'],$x,1,1);
                    } else {
                        $this->UpdateRelationshipTable($row['id'],$x,1,0);
                    }
                }
            }
            $ratio = (float) $count/ count($return_list);
            $this->UpdateMatchingRatio($row['id'], $ratio);
        }
    }
    public function CheckPrice($check_price){
        $base_price = 6290000;
        $num = $base_price - $check_price;
        if ($num < 0) $num = -1 * $num;
        $p = (float) $num / $base_price;
        if ($p > 0.2) return -1;
        return 1;
    }
    public function GetPrice($raw_string){
        $raw_price = implode("", explode(" ", $raw_string));
        $end = stripos($raw_price,"₫");
        if ($end == false) $end = stripos($raw_price, "đ");
        $start = $end - 1;

        $price = 0;
        $base = 1;
        while($start >= 0){
            $character = substr($raw_price, $start, $end-$start);
            if (is_numeric($character) || ($character == ".")){
                $end = $start;
                $start = $end-1;
                if ($character != "."){
                    $price += $base * intval($character);
                    $base *= 10;
                }
            } else{
                $start = -1;
            }
        }
        return $price;
    }
    public function SearchCompetitivePrice($product_name){
        $price = 0;
        //1. Look at dataset and get min price
        $price = $this->GetMinPrice($product_name);
        if ($price > 0){
           
            return $price;
        }
        //2. Generate link
        $min_price = -1;
        $return_list = $this->GetRelevantLinks($product_name);
        $this->BuildUpDataset($product_name, $return_list);
        $return_list1 = $this->GetAllLinks($product_name);
        foreach($return_list1 as $x => $x_value){
            //3. Look at rule
            $sql = "SELECT * FROM `rules` ORDER BY matching_ratio DESC";
            $db = new database;
            $result = $db->select($sql);
            $flag = 1;
            while (($flag == 1) && ($row = mysqli_fetch_array($result))){
                $num = $this->CheckRuleMatchLink($x_value, $row['class_or_id'], $row['name']);
                if ($num > 1) {
                    if (($min_price == -1)||($min_price > $num)){
                        $min_price = $num;
                        $flag = -1;
                    }
                }
            }
        }
        //Chua chinh xac
        echo $min_price;
    }
    public function GetMinPrice($product_name){
        $sql = "SELECT min(price) as price FROM `dataset` WHERE price > 0 AND product_name = '{$product_name}'";
        $db = new database;
        $result = $db->select($sql);
        $row = mysqli_fetch_array($result);
        //echo $row['price'];
         return $row['price'];
    }
    public function GetUnfriendlyLinks($product_name){
        //1. Get dataset
        $dataset_list = $this->GetAllLinks($product_name);
        //2. Get all rules
        $rule_list = $this->GetAllRules($product_name);
        //3. Check every link i Relationship table
        $return_list = array();
        foreach($dataset_list as $dataset_id => $link_name){
            $flag = 1;
            foreach($rule_list as $rule_id => $rule_name){
                $num = $this->CheckLinkMatchRule($dataset_id, $rule_id);
                if ($num == 1){
                    $flag = 0;
                }
            }
            if ($flag == 1){
                $return_list[$dataset_id] = $link_name;
            }
        }
        foreach($return_list as $x => $y){
            echo $x. "". $y . "<br>";
        }
    }
    public function CheckLinkMatchRule($dataset_id, $rule_id){
        $sql = "SELECT `is_identified_price` FROM `rules_and_dataset` WHERE dataset_id = {$dataset_id} AND rule_id = {$rule_id}";
        $db = new database;
        $result = $db->select($sql);
        $row = mysqli_fetch_array($result);
        return $row['is_identified_price'];
    }
    public function IsLearnd($rule_id, $dataset_id){
        $sql = "SELECT COUNT(*) as num FROM `rules_and_dataset` WHERE rule_id = {$rule_id} AND dataset_id = {$dataset_id}";
        $db = new database;
        $result = $db->select($sql);
        $row = mysqli_fetch_array($result);
        return $row['num'];
    }
    public function UpdateRelationshipTable($rule_id, $dataset_id, $is_visited, $is_price){
        $sql = "INSERT INTO `rules_and_dataset` (rule_id, dataset_id, is_visited, is_identified_price) VALUES ({$rule_id}, {$dataset_id}, {$is_visited}, {$is_price})";
        $db = new database;
        $db->insert($sql);
    }
    public function UpdateMatchingRatio($rule_id, $ratio){
        $sql = "UPDATE `rules` SET `matching_ratio`  = {$ratio} WHERE id = {$rule_id}";
        $db = new database;
        $result = $db->update($sql);
    }
    public function TestLink($link){
        $html = file_get_html($link);
        if ($html == false) return -1;
        return 1;
    }
    public function BuildUpDataset($product_name, $return_list){
        foreach($return_list as $x => $x_value){
            //1. Get link is not in dataset
            $test = $this->CheckLinkInDataset($x_value);
            set_error_handler(function() {});
            $check = $this->TestLink($x_value);
            restore_error_handler();
            //2. Insert this link
            if(($test == 0) && ($check == 1)){
                $sql = "INSERT INTO `dataset` (product_name, link_name) VALUES ('{$product_name}', '{$x_value}')";
                $db = new database;
                $db->insert($sql);
            }
        }
    }
    public function CheckLinkInDataset($link){
        $sql = "SELECT COUNT(*) as num FROM `dataset` WHERE link_name = '{$link}'";
        $db = new database;
        $result = $db->select($sql);
        $row = mysqli_fetch_array($result);
        return $row['num'];
    }
    public function FindPrice($link){
        $html = file_get_html($link);
        //$ret = $html->find('.area_price');
        //$test = '.area_price';
        //$test = '.fs-dtprice';
        //$test = '#_price_new436';
        //$test = '.price';
        $test = '.fs-dtprice';
        echo $test;
        foreach($html->find($test) as $element)
            echo $element . '<br>';
    }
    //standardize search string
    public function StandarizeLink($raw_link){
        $start = stripos($raw_link,"https");
        if ($start !== false)
        {
            $end = stripos($raw_link,"&");
            $link = substr($raw_link,$start,$end-$start);
            return $link;
        }
        return -1;
    }
    public function BuildSearchString($search){
        $list = explode(" ",$search);
        $result = "";
        for ($i = 0; $i < count($list)-1; $i ++)
            $result = $result . $list[$i] . "+";
        $result = $result . $list[$i];
        return $result;
    }
    public function GetView($product_id, $from, $to){
        $sql = "SELECT COUNT(*) as NUM 
        FROM product_analysis 
        WHERE product_id = {$product_id}
        AND visited_date > '{$from}' 
        AND visited_date < '{$to}'";
        $db = new database;
        $result = $db->select($sql);
        echo $result;
    }
    //Bang update view vao database
    public function UpdateViewToTable($product_id,$from, $to){
        $sql="UPDATE view_current set views_from_to=(SELECT COUNT(*) as NUM 
        FROM product_analysis 
        WHERE product_id = $product_id
        AND visited_date > '{$from}' 
        AND visited_date < '{$to}')
        where product_id=$product_id;" ;    
        $db = new database;
        $result = $db->update($sql);
        echo $result;
    }
    public function UpdateViewOfProduct($product_id){
        $now = date("Y-m-d H:i:s");
        $sql = "INSERT INTO product_analysis (product_id, visited_date) VALUES ({$product_id}, '{$now}')";
        $db = new database;
        $result = $db->insert($sql);
    }
}
?>