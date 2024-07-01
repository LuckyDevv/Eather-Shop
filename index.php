<?php
require 'src/php/database/ProductsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/database/AccountsDB.php';
require 'src/php/database/PersonalDB.php';
require 'src/php/Functions.php';
require 'src/php/ErrorManager.php';
$formatter = new Functions();
$products_db = new ProductsDB();
$top_products = $products_db->product_get_top_sales();
$new_products = $products_db->product_get_news();
$acc_id = $formatter->get_cookie_acc_id($_COOKIE);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="src/css/bootstrap.min.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="src/css/index.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo/logo.png">
    <title>EATHER - интернет-магазин</title>
</head>
<body>
<script src="src/js/jquery.min.js"></script>
<?php
if (is_array($top_products)){
    $top_products = $formatter->get_products($_COOKIE, 'Лидеры продаж!', $top_products, 5);
    if ($top_products !== '</div>') {
        echo '<div class="container-fluid m-7-top" style="background-color: #f0f0ec">
                '.$top_products.'
              </div>
              ';
    }
}else $top_products = '</div>';

if (is_array($new_products)){
    $new_products = $formatter->get_products($_COOKIE, 'Новинки на рынке!', $new_products, 5);
    if ($new_products !== '</div>') {
        echo '<div class="container-fluid m-7-top" style="background-color: #f0f0ec">
                '.$new_products.'
              </div>
              <br>';
    }
}else $new_products = '</div>';

if ($new_products == '</div>' && $top_products == '</div>') {
    echo '<div class="container-fluid m-7-top" style="background-color: #f0f0ec">
            <h2>В нашем интернет-магазине пока что нет продуктов! :(<br>Скоро мы это исправим!</h2>
          </div>';
}
?>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/index.js"></script>
<script src="src/js/auth.js"></script>
<?php
if (isset($_GET['toast']))
{
    if ($_GET['toast'] == 'no_auth')
    {
        echo '<script>no_auth = true;</script>';
    }
}
?>
<?php echo $formatter->get_header_script(); // ?>
<script src="src/js/bootstrap.min.js"></script>
</body>
</html>