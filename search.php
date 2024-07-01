<?php
require 'src/php/database/ProductsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/database/AccountsDB.php';
require 'src/php/database/PersonalDB.php';
require 'src/php/Functions.php';
require 'src/php/ErrorManager.php';
$formatter = new Functions();
$find_name = $_GET['find_name'] ?? false;
$acc_id = $formatter->get_cookie_acc_id($_COOKIE);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="src/css/bootstrap.min.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/index.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo/logo.png">
    <title>Результаты поиска - EATHER</title>
</head>
<body>
<?php echo $formatter->get_header(); ?>
<div class="container-fluid" style="background-color: #f0f0ec">
    <br>
    <?php
    $find_me = $find_name !== false ? (new ProductsDB())->product_find_by_name(mb_strtolower(trim($find_name), 'utf-8')) : false;
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
<script src="src/toastr/toastr.js"></script>
<script src="src/js/index.js"></script>
<script src="src/js/auth.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<?php echo $formatter->get_header_script();
if($find_name !== false) {
    echo "
<script>
    document.getElementById('search_param').value = '".$find_name."';
</script>
";
}
?>
</body>
</html>