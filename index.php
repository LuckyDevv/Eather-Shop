<?php
require 'src/php/ProductsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/AccountsDB.php';
require 'src/php/PersonalDB.php';
require 'src/php/Functions.php';
$formatter = new Functions();
$products_db = new ProductsDB('src/php/database/Products_DB.db');
$top_products = $products_db->product_get_top_sales();
$new_products = $products_db->product_get_news();
$acc_id = $formatter->get_cookie_acc_id($_COOKIE);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link href="src/css/bootstrap.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/index.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <title>EATHER - интернет-магазин</title>
</head>
<body>
<?php echo $formatter->get_header();
    $top_products = $formatter->get_products($_COOKIE, 'Лидеры продаж!', $top_products, 5);
    $new_products = $formatter->get_products($_COOKIE, 'Новинки на рынке!', $new_products, 5);
    if ($top_products !== '</div>') {
        echo '<div class="container-fluid" style="background-color: #f0f0ec">
                <br>'.$top_products.'
              </div>
              ';
    }
    if ($new_products !== '</div>') {
        echo '<div class="container-fluid" style="background-color: #f0f0ec">
                <br>'.$new_products.'
              </div>
              <br>';
}
?>
<script src="src/js/jquery.min.js"></script>
<script src="src/js/index.js"></script>
<?php
if (isset($_GET['toast']))
{
    if ($_GET['toast'] == 'no_auth')
    {
        echo '<script>no_auth = true;</script>';
    }
}
?>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<script src="src/js/popper.min.js"></script>
</body>
</html>