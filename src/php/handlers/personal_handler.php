<?php
require '../database/AccountsDB.php';
require '../ConfigController.php';
require '../database/PersonalDB.php';
require '../ErrorManager.php';
const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;
function error($message, $code): void
{
    die(json_encode(['error' => ['message' => $message, 'code' => $code]], JSON_OPTIONS));
}
function response($message): void
{
    die(json_encode(['response' => $message], JSON_OPTIONS));
}

$post = $_POST['type'] ?? null;
$personal_db = new PersonalDB();
switch ($post) {
    case 'delete_favorite':
        $product_id = $_POST['product_id'] ?? ''; $product_id = $product_id !== '' ? $product_id : null;
        $acc_id = $_POST['acc_id'] ?? ''; $acc_id = $acc_id !== '' ? $acc_id : null;
        if (is_numeric($product_id) && is_numeric($acc_id))
        {
            $acc_id = (int) $acc_id;
            $product_id = (int) $product_id;
            if ($personal_db->favorites_delete_product($acc_id, $product_id))
            {
                response('Успешно!');
            }else error('Не удалось удалить товар из избранного!', 1);
        }else error('ID аккаунта и ID товара не являются числами!', 2);
        break;
    case 'add_favorite':
        $product_id = $_POST['product_id'] ?? ''; $product_id = $product_id !== '' ? $product_id : null;
        $acc_id = $_POST['acc_id'] ?? ''; $acc_id = $acc_id !== '' ? $acc_id : null;
        if (is_numeric($product_id) && is_numeric($acc_id))
        {
            $acc_id = (int) $acc_id;
            $product_id = (int) $product_id;
            if ($personal_db->favorites_add_product($acc_id, $product_id))
            {
                response('Успешно!');
            }else error('Не удалось добавить товар в избранное!', 1);
        }else error('ID аккаунта и ID товара не являются числами!', 2);
        break;
    case 'delete_cart':
        $product_id = $_POST['product_id'] ?? ''; $product_id = $product_id !== '' ? $product_id : null;
        $acc_id = $_POST['acc_id'] ?? ''; $acc_id = $acc_id !== '' ? $acc_id : null;
        if (is_numeric($product_id) && is_numeric($acc_id))
        {
            $acc_id = (int) $acc_id;
            $product_id = (int) $product_id;
            if ($personal_db->cart_delete_product($acc_id, $product_id))
            {
                response('Успешно!');
            }else error('Не удалось удалить товар из корзины!', 1);
        }else error('ID аккаунта и ID товара не являются числами!', 2);
        break;
    case 'add_cart':
        $product_id = $_POST['product_id'] ?? ''; $product_id = $product_id !== '' ? $product_id : null;
        $acc_id = $_POST['acc_id'] ?? ''; $acc_id = $acc_id !== '' ? $acc_id : null;
        if (is_numeric($product_id) && is_numeric($acc_id))
        {
            $acc_id = (int) $acc_id;
            $product_id = (int) $product_id;
            if ($personal_db->cart_add_product($acc_id, $product_id))
            {
                response('Успешно!');
            }else error('Не удалось добавить товар в корзину!', 1);
        }else error('ID аккаунта и ID товара не являются числами!', 2);
        break;
}
