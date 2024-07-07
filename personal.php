<?php
require 'src/php/database/AccountsDB.php';
require 'src/php/ConfigController.php';
require 'src/php/database/PersonalDB.php';
require 'src/php/database/ProductsDB.php';
require 'src/php/Functions.php';
require 'src/php/ErrorManager.php';
$formatter = new Functions();
$accounts_db = new AccountsDB();
$personal_db = new PersonalDB();
if(!$formatter->get_cookie_auth($_COOKIE, $_SERVER['REMOTE_ADDR']))
{
    quit();
}
function quit(): void
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
    <link href="src/css/personal.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo/logo.png">
    <title>EATHER - Личный кабинет</title>
</head>
<body>
<?php
if (isset($_COOKIE['sess_id']))
{
    $acc_id = $formatter->get_session_acc_id($_COOKIE['sess_id']);
    if ($acc_id === 0){
        quit();
    }
}else{
    quit();
}

if (isset($acc_id))
{
    $devices = '';
    foreach (explode(',', $accounts_db->devices_get($acc_id)) as $device) {
        $devices .= $device.'<br>';
    }

    echo str_replace(array(
        '%avatar%',
        '%username%',
        '%login%',
        '%account_id%',
        '%wallet_id%',
        '%devices%'
    ), array(
        $accounts_db->avatar_get($acc_id),
        $accounts_db->name_get($acc_id),
        $accounts_db->login_get($acc_id),
        $acc_id,
        $personal_db->get_wallet_id($acc_id),
        rtrim($devices, '<br>')
    ), $formatter->getTemplate('personal'));
}
?>

<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Смена фото профиля</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="mb-5">
                    <input class="form-control" type="file" accept="image/png, image/jpeg" id="formFile" onchange="preview()">
                    <button id="clear_image_button" class="btn btn-danger mt-3">Удалить изображение</button>
                    <button id="upload_image_button" class="btn btn-success mt-3">Сохранить</button>
                </div>
                <img id="frame" src="" class="img-fluid"  alt=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script src="src/js/jquery.min.js"></script>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/index.js"></script>
<script src="src/js/personal.js"></script>
<script src="src/js/main.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<?php echo $formatter->get_header_script();
if (!$formatter->get_cookie_auth($_COOKIE, $_SERVER['REMOTE_ADDR'])) {
    echo '<script>change = true;</script>';
}
?>
</body>
</html>