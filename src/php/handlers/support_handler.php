<?php
require '../ConfigController.php';
require '../database/AccountsDB.php';
require '../Functions.php';
require '../ErrorManager.php';
date_default_timezone_set('Europe/Moscow');
const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;
function error($message, $code): void
{
    die(json_encode(['error' => ['message' => $message, 'code' => $code]], JSON_OPTIONS));
}
function response($message): void
{
    die(json_encode(['response' => $message], JSON_OPTIONS));
}
function send_html($message, $html): void
{
    die(json_encode(['block' => ['message' => $message, 'html' => $html]], JSON_OPTIONS));
}
function send_message($message, $html, $ids): void
{
    die(json_encode(['block' => ['message' => $message, 'html' => $html, 'ids' => $ids]], JSON_OPTIONS));
}
function send_chat($message, $chat, $messages, $input, $chat_id): void
{
    die(json_encode(['block' => ['message' => $message, 'chat' => $chat, 'messages' => $messages, 'input' => $input, 'chat_id' => $chat_id]], JSON_OPTIONS));
}
function send_new_chat($message, $chat, $messages, $input, $chat_id, $tickets): void
{
    die(json_encode(['block' => ['message' => $message, 'chat' => $chat, 'messages' => $messages, 'input' => $input, 'chat_id' => $chat_id, 'tickets' => $tickets]], JSON_OPTIONS));
}
$functions = new Functions();


@mkdir('chats');
if (isset($_POST['sess_id']))
{
    $acc_id = $functions->get_session_acc_id($_POST['sess_id'], true);
    if ($acc_id === 0) error('Не авторизирован!', 999);
    @mkdir('../chats/' . $acc_id);
    if (isset($_POST['chat_id']))
    {
        $chat_id = (string) $_POST['chat_id'];
        if (is_numeric($chat_id))
        {
            @mkdir('../chats/' . $acc_id . '/' . $chat_id);
            if (is_dir('../chats/'.$acc_id.'/'.$chat_id))
            {
                try {
                    $chat_file = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/messages.json', ConfigController::JSON);
                } catch (ErrorException $e) {
                    error('Ошибка открытия конфига чата!', 409);
                }
                try {
                    $status = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/status.json', ConfigController::JSON);
                } catch (ErrorException $e) {
                    error('Ошибка открытия конфига чата!', 409);
                }
                try {
                    $last_messages = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/last.json', ConfigController::JSON);
                } catch (ErrorException $e) {
                    error('Ошибка создания конфига чата!', 409);
                }
            }else error('Не найден чат с таким ID!', 2);
        }else error('Введен несуществующий ID чата!', 99);
    }
}else error('Не введен ID сессии!', 99);
switch ($_POST['type'] ?? 'null')
{
    case 'send_message':
        if(isset($chat_file, $status, $last_messages))
        {
            if ($status->get('status') === 0)
            {
                if (isset($_POST['message']))
                {
                    $message = trim((string) $_POST['message']);
                    if ($message !== '')
                    {
                        // TODO: Сделать проверку, это тех поддержка или нет
                        $sender = 'user';
                        $all = $chat_file->getAll();
                        $last_messages->setAll($all);
                        $time = time();
                        $message_id = $status->get('last_message_id') + 1;
                        $all[] = [
                            'sender' => $sender,
                            'time' => $time,
                            'message' => $message,
                            'status' => 0,
                            'message_id' => $message_id
                        ];
                        $chat_file->setAll($all);
                        $status->set('last_message_id', $message_id);
                        try {
                            $message_tpl = $sender == 'user' ? 'chat/message2' : 'chat/message';
                            $last_messages->save();
                            $status->save();
                            $chat_file->save();
                            send_html('OK', str_replace(array(
                                '%message_id%',
                                '%time%',
                                '%sender%',
                                '%message%'
                            ), array(
                                $message_id,
                                date('H:i', $time),
                                $sender,
                                $message,
                            ), getTemplate($message_tpl)));
                        } catch (ErrorException $e) {
                            error('Ошибка заполнения базы данных!', 3421);
                        }
                    }else error('Не заполнено сообщение', 5);
                }else error('Не заполнено сообщение', 4);
            }else error('Не найден файл статуса', 3);
        }else error('Файл статуса и файл чата не найдены!', 12);
        break;
    case 'get_tickets':
        if (isset($acc_id))
        {
            $tickets = getTickets($acc_id);
            if ($tickets !== '')
            {
                send_html('OK', $tickets);
            }else{
                response('99');
            }
        }else error('Не выявлен ID пользователя: ', 9909);
        break;
    case 'get_chat':
        if(isset($chat_file, $status, $chat_id, $last_messages, $acc_id))
        {
            $messages = '';
            $all = $chat_file->getAll();
            foreach ($all as $message)
            {
                $message_tpl = $message['sender'] == 'user' ? 'chat/message2' : 'chat/message';
                $messages .= str_replace(array(
                    '%message_id%',
                    '%time%',
                    '%sender%',
                    '%message%',
                ), array(
                    $message['message_id'],
                    date('H:i', $message['time']),
                    $message['sender'],
                    $message['message'],
                ),
                    getTemplate($message_tpl));
            }
            $status = $status->get('status');
            if ($status === 0)
            {
                $input = getTemplate('chat/input_chat_1');
                $status_text = 'В работе';
            }else{
                $input = getTemplate('chat/input_chat_2');
                $status_text = 'Закрыто';
            }
            $chat = str_replace(array(
                '%ticket%',
                '%status%'
            ), array(
                $ticket = '№'.$acc_id.'_'.$chat_id,
                $status_text
            ),
                getTemplate('chat/chat'));
            $last_messages->setAll($all);
            $last_messages->save();
            send_chat('Загружено', $chat, $messages, $input, $chat_id);
        }else error('Файл статуса и файл чата не найдены!', 13);
        break;
    case 'get_new_message':
        if(isset($chat_file, $status, $last_messages))
        {
            $new = $chat_file->getAll();
            $old = $last_messages->getAll();
            /*$if (count($new) > count($old))
            {*/
                $difference = array_slice($new, count($old));
                $messages = [];
                $message_ids = '';
                $sender = 'user';
                foreach ($new as $message)
                {
                    $message_ids .= $message['message_id'] . ',';
                    $message_tpl = $message['sender'] == 'user' ? 'chat/message2' : 'chat/message';
                    $messages[] = str_replace(array(
                        '%message_id%',
                        '%time%',
                        '%sender%',
                        '%message%',
                    ), array(
                        $message['message_id'],
                        date('H:i', $message['time']),
                        $message['sender'],
                        $message['message'],
                    ),
                        getTemplate($message_tpl));
                }
                if (count($messages) !== 0){
                    $last_messages->setAll($new);
                    try{
                        $last_messages->save();
                        send_message('Новые сообщения ('.count($difference).')', $messages, rtrim($message_ids, ','));
                    }catch (ErrorException $e){}
                }
            //}
        }else error('Не найден чат!', 11);
        break;
    case 'create_chat':
        if(isset($acc_id)) {
            $opened_chats = 0;
            $last_chat_id = 0;
            @mkdir('../chats', 0777);
            @mkdir('../chats/'.$acc_id, 0777);
            foreach (scandir('../chats/'.$acc_id) as $ticket) {
                if (!is_numeric($ticket)) continue;
                try {
                    $temp_status = new ConfigController('../chats/' . $acc_id . '/' . $ticket . '/status.json', ConfigController::JSON);
                } catch (ErrorException $e) {
                    error('Ошибка создания конфига чата!', 409);
                }
                if (isset($temp_status))
                {
                    if ($temp_status->get('status') === 0) $opened_chats++;
                }
                if ((int) $ticket > $last_chat_id) $last_chat_id = (int) $ticket;
            }

            if ($opened_chats >= 2)
            {
                error('У вас уже открыто '.$opened_chats.' обращений!', 11);
            }
            $chat_id = $last_chat_id + 1;

            @mkdir('../chats/' . $acc_id . '/' . $chat_id, 0777);
            try {
                $chat_file = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/messages.json', ConfigController::JSON);
            } catch (ErrorException $e) {
                error('Ошибка создания конфига чата!', 409);
            }
            try {
                $status = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/status.json', ConfigController::JSON);
            } catch (ErrorException $e) {
                error('Ошибка создания конфига чата!', 409);
            }
            try {
                $last_messages = new ConfigController('../chats/' . $acc_id . '/' . $chat_id . '/last.json', ConfigController::JSON);
            } catch (ErrorException $e) {
                error('Ошибка создания конфига чата!', 409);
            }
            if (isset($chat_file, $status, $chat_id, $last_messages))
            {
                $status->setAll([
                    'status' => 0,
                    'feedback_rate' => null,
                    'supporter' => null,
                    'end_time' => null,
                    'last_message_id' => 1
                ]);
                $all = $chat_file->getAll();
                $all[] = [
                    'sender' => 'Система',
                    'time' => time(),
                    'message' => 'Вас приветствует техническая поддержка интернет-магазина EATHER. Какой у вас вопрос?',
                    'status' => 0,
                    'message_id' => 1,
                ];
                $chat_file->setAll($all);
                try {
                    $status->save();
                    $chat_file->save();
                    $messages = '';
                    $all = $chat_file->getAll();
                    if (count($all) > 0)
                    {
                        foreach ($all as $message)
                        {
                            $message_tpl = $message['sender'] == 'user' ? 'chat/message2' : 'chat/message';
                            $messages .= str_replace(array(
                                '%message_id%',
                                '%time%',
                                '%sender%',
                                '%message%',
                            ), array(
                                $message['message_id'],
                                date('H:i', $message['time']),
                                $message['sender'],
                                $message['message'],
                            ),
                                getTemplate($message_tpl));
                        }
                    }
                    $status = $status->get('status');
                    if ($status === 0)
                    {
                        $input = getTemplate('chat/input_chat_1');
                        $status_text = 'В работе';
                    }else{
                        $input = getTemplate('chat/input_chat_2');
                        $status_text = 'Закрыто';
                    }
                    $chat = str_replace(array(
                        '%ticket%',
                        '%status%'
                    ), array(
                        $ticket = '№'.$acc_id.'_'.$chat_id,
                        $status_text
                    ),
                        getTemplate('chat/chat'));
                    $last_messages->setAll($all);
                    $last_messages->save();
                    send_new_chat('Загружено', $chat, $messages, $input, $chat_id, getTickets($acc_id));
                } catch (ErrorException $e) {
                    error('Не удалось сохранить конфигурации', 3123);
                }
            }else error('Не удалось создать чат!', 9999);
        }else error('Не введён параметр сессии!', 9778);
        break;
    default:
        error('Неизвестная операция!', 9);
        break;
}

function getTemplate(string $template): false|string
{
    ob_start();
    include('../tpl/' . $template . ".tpl");
    return ob_get_clean();
}

function getTickets(int $acc_id): string
{
    @mkdir('../chats/'.$acc_id);
    $ret = '';
    foreach (scandir('../chats/'.$acc_id) as $ticket)
    {
        if (!is_numeric($ticket)) continue;
        $path = '../chats/'.$acc_id.'/'.$ticket;
        if (!is_file($path.'/messages.json') && !is_file($path.'/status.json')) continue;
        try {
            new ConfigController($path.'/messages.json', ConfigController::JSON);
        } catch (ErrorException $e) {
            continue;
        }
        try {
            $status = new ConfigController($path.'/status.json', ConfigController::JSON);
        } catch (ErrorException $e) {
            continue;
        }
        $status = $status->get('status');
        $ret .= str_replace(array(
            '%ticket_number%',
            '%ticket%',
            '%circle%',
            '%status%'
        ), array(
            $ticket,
            '№'.$acc_id.'_'.$ticket,
            $status == 0 ? 'online' : 'offline',
            $status == 0 ? 'В работе' : 'Закрыто'
        ),
            getTemplate('chat/chats'));
    }
    return trim($ret);
}