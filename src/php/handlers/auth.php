<?php
require '../database/PersonalDB.php';
require '../database/AccountsDB.php';
require '../ConfigController.php';
require '../ErrorManager.php';
const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;

$post = $_POST['type'] ?? null;
$accounts_db = new AccountsDB();

/**
 * @param string $login
 * @param string $hash_password
 * @return void
 */
function createSession(string $login, string $hash_password): void
{
    $id = hash('sha256', generateRandomString(12));
    try {
        $session_config = new ConfigController('../sessions/' . $id . '.json', ConfigController::JSON);
        $session_config->setAll(['login' => $login, 'password' => $hash_password, 'timeout' => time() + 604800]);
        $session_config->save();
        response($id);
    } catch (ErrorException $e) {
        error('Произошла ошибка создания сессии!', 5);
    }
}

function generateRandomString($length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function error($message, $code): void
{
    die(json_encode(['error' => ['message' => $message, 'code' => $code]], JSON_OPTIONS));
}

function response($message): void
{
    die(json_encode(['response' => $message], JSON_OPTIONS));
}

switch ($post)
{
    case 'registration':
        $login = $_POST['login'] ?? ''; $login = strtolower($login); $login = $login !== '' ? $login : null;
        $password = $_POST['password'] ?? null; $password = $password !== '' ? $password : null;
        $password_confirm = $_POST['password_confirm'] ?? null; $password_confirm = $password_confirm !== '' ? $password_confirm : null;
        if ($login !== null && $password !== null && $password_confirm !== null)
        {
            if(preg_match('|^[A-Z0-9]+$|i', $login))
            {
                if (!$accounts_db->account_exists_login($login))
                {
                    $hash_password = hash('sha256', $password);
                    $hash_password_confirm = hash('sha256', $password_confirm);
                    if ($hash_password == $hash_password_confirm)
                    {
                        if ($accounts_db->account_registration($login, $hash_password, $_SERVER['REMOTE_ADDR'], '../database/database/Personal_DB.db'))
                        {
                            createSession($login, $hash_password);
                        }else error('Не удалось провести регистрацию!', 4);
                    }else error('Подтверждение пароля не совпадает с паролем!', 3);
                }else error('Аккаунт с таким логином уже зарегистрирован!', 2);
            }else error('Логин может состоять только из английских букв и цифр!', 1);
        }else error('Не введены все значения!', 0);
        break;

    case 'auth':
        $login = $_POST['login'] ?? '';  $login = strtolower($login); $login = $login !== '' ? $login : null;
        $password = $_POST['password'] ?? null; $password = $password !== '' ? $password : null;
        if ($login !== null && $password !== null)
        {
            if(preg_match('|^[A-Z0-9]+$|i', $login))
            {
                if ($accounts_db->account_exists_login($login))
                {
                    $hash_password = hash('sha256', $password);
                    if ($accounts_db->account_auth($login, $hash_password, $_SERVER['REMOTE_ADDR']))
                    {
                        createSession($login, $hash_password);
                    }else error('Не удалось провести авторизацию!', 4);
                }else error('Аккаунта с таким логином не существует!', 2);
            }else error('Логин может состоять только из английских букв и цифр!', 1);
        }else error('Не введены все значения!', 0);
        break;
}