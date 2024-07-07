<?php
require '../Functions.php';
require '../database/AccountsDB.php';
require '../ConfigController.php';
require '../database/PersonalDB.php';
require '../ErrorManager.php';
$functions = new Functions();
$accounts_db = new AccountsDB();
const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;
function error($message, $code): void
{
    die(json_encode(['error' => ['message' => $message, 'code' => $code]], JSON_OPTIONS));
}

function response($message): void
{
    die(json_encode(['response' => $message], JSON_OPTIONS));
}

function warning($message): void
{
    die(json_encode(['warning' => $message], JSON_OPTIONS));
}

if (isset($_POST['sess_id'])) {
    $acc_id = $functions->get_session_acc_id($_POST['sess_id'], true);
    if ($acc_id === 0) error('Не авторизирован!', 999);
}else error('Не введен ID сессии', 999);

if (($_POST['type'] ?? null) == 'set') {
    if (isset($acc_id)) {
        if (isset($_POST['source']['login'])) {
            $login_changed = false;
            $_new_login = $_POST['source']['login'];
            if (preg_match('|^[A-Z0-9]+$|i', $_new_login)) {
                $_old_login = $accounts_db->login_get($acc_id);
                if ($_old_login !== $_new_login) {
                    $login_changed = $accounts_db->login_set($acc_id, $_new_login);
                    if ($login_changed) {
                        $session_config = new ConfigController('../sessions/' . $_POST['sess_id'] . '.json', ConfigController::JSON);
                        $session_config->set('login', $_new_login);
                        $session_config->save();
                    }
                }
            }
        }
        if (isset($_POST['source']['username'])) {
            $name_changed = false;
            $_new_name = $_POST['source']['username'];
            $_old_name = $accounts_db->name_get($acc_id);
            if ($_old_name !== $_new_name) {
                $name_changed = $accounts_db->name_set($acc_id, $_new_name);
            }
        }
        if (isset($_POST['source']['password'], $_POST['source']['password_confirm'])) {
            $password_changed = false;
            if ($_POST['source']['password'] == $_POST['source']['password_confirm']) {
                $_new_password = hash('sha256', $_POST['source']['password']);
                $password_changed = $accounts_db->password_set($acc_id, $_new_password);
                if ($password_changed) {
                    $session_config = new ConfigController('../sessions/' . $_POST['sess_id'] . '.json', ConfigController::JSON);
                    $session_config->set('password', $_new_password);
                    $session_config->save();
                }
            }
        }
        $not_changed = '';
        if (isset ($login_changed)) {
            if (!$login_changed) {
                $not_changed .= 'Логина';
            }
        }
        if (isset ($name_changed)) {
            if (!$name_changed) {
                $not_changed .= 'Имени';
            }
        }
        if (isset ($password_changed)) {
            if (!$password_changed) {
                $not_changed .= 'Пароля';
            }
        }
        if ($not_changed === '') {
            response('Успешно сохранено!');
        }else{
            warning('Успешно сохранено, кроме: <b>'.$not_changed.'</b>');
        }
    } else error('Не найден ID аккаунта!', 8);
}elseif (($_POST['type'] ?? null) == 'quit'){
    if (isset($acc_id)) {
        unlink('../sessions/' . $_POST['sess_id'] . '.json');
        response('Success');
    }else error('Не введён ID сессии', 11);
} else {
    if ($_FILES['file']['name'] !== "") {
        if (isset($acc_id)) {
            $ext = array_reverse(explode(".", $_FILES["file"]["name"]))[0];
            @mkdir('../../../photos/');
            @mkdir('../../../photos/users/');
            $saveto = "../../../photos/users/$acc_id.$ext";
            move_uploaded_file($_FILES['file']['tmp_name'], $saveto);
            $typeok = TRUE;
            switch ($_FILES['file']['type']) {
                case "image/jpeg": // Both regular and progressive jpegs
                case "image/pjpeg":
                    $src = imagecreatefromjpeg($saveto);
                    break;
                case "image/png":
                    $src = imagecreatefrompng($saveto);
                    break;
                default:
                    $typeok = FALSE;
                    break;
            }
            if ($typeok) {
                list($x, $y) = getimagesize($saveto);
                if ($x > $y) {
                    $square = $y;
                    $offsetX = ($x - $y) / 2;
                    $offsetY = 0;
                }
                elseif ($y > $x) {
                    $square = $x;
                    $offsetX = 0;
                    $offsetY = ($y - $x) / 2;
                }
                else {
                    $square = $x;
                    $offsetX = $offsetY = 0;
                }
                $endSize = 512;
                $tmp = imagecreatetruecolor($endSize, $endSize);
                imagecopyresampled($tmp, $src, 0, 0, $offsetX, $offsetY, $endSize, $endSize, $square, $square);
                imageconvolution($tmp, array(
                    array(-1, -1, -1),
                    array(-1, 16, -1),
                    array(-1, -1, -1)
                ), 8, 0);
                imagejpeg($tmp, $saveto);
                imagedestroy($tmp);
                imagedestroy($src);
                $accounts_db->avatar_set($acc_id, $saveto) ? response('Успешно!') : error('Не удалось обновить данные', 1);
            } else error('Загружен не поддерживаемый тип', 2);
        } else error('Не найден ID аккаунта!', 3);
    } else error('Ошибка загрузки файла!', 4);
}