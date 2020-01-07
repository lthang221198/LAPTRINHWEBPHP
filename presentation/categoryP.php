<style>
    <?php include 'include/assets/css/categoryP.css'; ?>
</style>

<?php include "business/categoryB.php"; ?>
<?php
// $test = new CategoryP;
// $test->ShowAllCategories();
class CategoryP
{
    public function GetCategory()
    {
        $category_id = 0;
        if (!isset($_GET['category'])) {
            $category_id = 0;
        } else {
            $category_id = $_GET['category'];
        }
        return $category_id;
    }

    public function GetGroup()
    {
        $product_group = 0;
        if (!isset($_GET['product_group'])) {
            $product_group = 1;
        } else {
            $product_group = $_GET['product_group'];
        }
        return $product_group;
    }
    public function SetStyleForCurrentCategory($cat_id)
    {
        $style = "";
        $current_cat = $this->GetCategory();
        if ($current_cat == $cat_id) {
            $style = "style='color: white; background: #00a8ff; text-decoration: none'";
        } else {
            $style = "style='color: #00a8ff; background: white; text-decoration: none'";
        }
        return $style;
    }
    public function ShowAllCategories()
    {
        $cb = new categoryB();
        $result = $cb->GetAllCategories();
        while ($row = mysqli_fetch_array($result)) {
            $category = <<<DELIMITER
             <a  href="index.php?category={$row['cat_id']}&product_group=1" {$this->SetStyleForCurrentCategory($row['cat_id'])} class="list-group-item">{$row['cat_name']}</a> 
            DELIMITER;
            echo $category;
        }
    }
    public function ShowLinkPagination()
    {
        $cb = new categoryB();
        $current_cat = $this->GetCategory();
        $num = $cb->CalculateNumberOfLinks($current_cat);
        for ($x = 1; $x <= $num; $x++) {
            if ($x < $num) {
                $link = <<<DELIMITER
                <li class="page-item">
                    <a class="page-link" href="index.php?category={$current_cat}&product_group={$x}">
                        {$x}
                    </a>
                </li>
                DELIMITER;
                echo $link;
            } else {
                $link = <<<DELIMITER
                <li class="page-item">
                    <a id="maxGroupNumber" numberValue="{$x}" class="page-link" href="index.php?category={$current_cat}&product_group={$x}">
                        {$x}
                    </a>
                </li>
                DELIMITER;
                echo $link;
            }
        }
    }
}
?>