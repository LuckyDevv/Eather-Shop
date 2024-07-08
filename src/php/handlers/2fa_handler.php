<?php

require '../ConfigController.php';
require '../Functions.php';
require '../database/AccountsDB.php';
require '../GoogleAuth/GoogleQrUrl.php';
require '../GoogleAuth/GoogleAuthenticator.php';
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

@mkdir('temp');

if (isset($_POST['sess_id'])) {
    $acc_id = $functions->get_session_acc_id($_POST['sess_id'], true);
}

$ga = new GoogleAuthenticator();

switch($_POST['type'] ?? null)
{
    case 'disable':
        if (isset($acc_id) && $acc_id !== 0)
        {
            if ($accounts_db->two_fa_delete($acc_id))
            {
                response('Успешно!');
            }else error('Не удалось записать в базу данных!', 5);
        }else error('Не авторизирован!', 999);
        break;
    case 'check_code':
        if (isset($acc_id) && $acc_id !== 0)
        {
            if (isset($_POST['code']))
            {
                if (is_numeric($_POST['code']))
                {
                    $secret_code = $accounts_db->two_fa_get_code($acc_id);
                    if ($accounts_db->two_fa_enabled($acc_id) && $secret_code !== false && $secret_code !== 'none')
                    {
                        if($ga->checkCode($secret_code, $_POST['code']))
                        {
                            response('Успешно!');
                        }else error('Неверный код!', 4);
                    }else error('2FA не включена!', 3);
                }else error('Код должен состоять из чисел!', 2);
            }else error('Не переданы требуемые данные!', 41);
        }else error('Не авторизирован!', 999);
        break;
    case 'confirm':
        if (isset($acc_id) && $acc_id !== 0)
        {
            if (isset ($_POST['secret_code'], $_POST['confirm_code']))
            {
                if ($ga->checkCode($_POST['secret_code'], $_POST['confirm_code']))
                {
                    if ($accounts_db->two_fa_set($acc_id, true, $_POST['secret_code']))
                    {
                        response('Успех!');
                    }else error('Не удалось создать запись 2FA!', 1);
                }else error('Неверный код подтверждения!', 8);
            }else error('Не был передан Secret Code!', 40);
        }else error('Не авторизирован!', 999);
        break;
    case 'generate':
        if (isset($acc_id) && $acc_id !== 0)
        {
            try {
                $csc = create_secret_code($ga, $accounts_db, $acc_id);
                if (is_array($csc))
                {
                    response(['img' => $csc[1], 'secret_code' => $csc[0], 'text' => 'Успешно!']);
                }else error('Не удалось создать Secret Code!', 11);
            } catch(Exception $e) {
                error('Произошла серверная ошибка!', 55);
            }
        }else error('Не авторизирован!', 999);
        break;
    case 'delete_secret':
        if (isset($_POST['secret']))
        {
            $filename = 'temp/'.$_POST['secret'];
            if (file_exists($filename)){
                unlink($filename);
                response('Yes');
            }else error('No', 991);
        }else error('No', 990);
}

function create_secret_code(GoogleAuthenticator $ga, AccountsDB $accounts_db, int $acc_id): array|false
{
    $secret_code = $ga->generateSecret();
    if (!file_exists($secret_code)){
        try {
            $user_name = $accounts_db->login_get($acc_id); // Имя пользователя
            $temp = new ConfigController('temp/'.$secret_code, ConfigController::JSON);
            $temp->set('secret_code', $secret_code);
            $temp->save();
            return array($secret_code, GoogleQrUrl::generate($user_name, $secret_code, 'EATHER Auth'));
        }catch(Exception) {
            return false;
        }
    }else return create_secret_code($ga, $accounts_db, $acc_id);
}