<?php
require 'src/php/ProductsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/AccountsDB.php';
require 'src/php/PersonalDB.php';
require 'src/php/Formatter.php';
$formatter = new Formatter();
$find_name = $_GET['find_name'] ?? false;
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
    <title>Результаты поиска - EATHER</title>
</head>
<body>
<?php echo $formatter->get_header(); ?>
<div class="container-fluid" style="background-color: #f0f0ec">
    <br>
    <?php
    $find_me = $find_name !== false ? (new ProductsDB('src/php/database/Products_DB.db'))->product_find_by_name(strtolower($find_name)) : false;
    if($find_me !== false)
    {
        echo $formatter->get_products($_COOKIE, 'Результат поиска:', $find_me, isSearch: true);
    }else{
        echo '<h1>К сожалению, ничего не нашлось ;(</h1>';
    }
    ?>
    <br>
</div>
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