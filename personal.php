<?php
require 'src/php/AccountsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/PersonalDB.php';
require 'src/php/ProductsDB.php';
require 'src/php/Formatter.php';
$formatter = new Formatter();
if(!$formatter->get_cookie_auth($_COOKIE)) header('Location: index.php?toast=no_auth');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link href="src/css/bootstrap.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/index.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <title>EATHER - Личный кабинет</title>
</head>
<body>
<?php echo $formatter->get_header(); ?>
<div class="container-fluid" style="background-color: #f0f0ec">
    <?php echo $formatter->get_personal_products('Избранное', 0); ?>
    <?php echo $formatter->get_personal_products('Корзина', 1); ?>
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
