<?php
require 'src/php/database/AccountsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/database/PersonalDB.php';
require 'src/php/database/ProductsDB.php';
require 'src/php/Functions.php';
require 'src/php/ErrorManager.php';
$formatter = new Functions();
if(!$formatter->get_cookie_auth($_COOKIE, $_SERVER['REMOTE_ADDR']))
{
    header('Location: index.php?toast=no_auth');
    unset($_COOKIE['sess_id']);
    setcookie('sess_id', '', '-1', '');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="src/css/bootstrap.min.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/index.css" rel="stylesheet">
    <link href="src/css/personal.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo/logo.png">
    <title>EATHER - Личный кабинет</title>
</head>
<body>
<div class="container-fluid" style="background-color: #f0f0ec">
    <br>
</div>
<script src="src/js/jquery.min.js"></script>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/index.js"></script>
<script src="src/js/auth.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<?php echo $formatter->get_header_script(); ?>
</body>
</html>
