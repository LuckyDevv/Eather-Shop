no_auth = false;
let shift = false;
let enter = false;
let sleep = 0;
$(function(){
    // инициализации подсказок для всех элементов на странице, имеющих атрибут data-toggle="tooltip"
    $('[data-toggle="tooltip"]').tooltip();
});
function updateChatList(){
    let chat_list = document.getElementById('chat_list');
    $.post('src/php/handlers/support_handler.php', {'type': 'get_tickets', 'sess_id': getCookie('sess_id')}, function (data){
        console.log('Запрос был отправлен!');
        const data_parsed = $.parseJSON(data);
        if(!empty(data_parsed.error))
        {
            if(data_parsed.error.code === 9909)
            {
                location.href = 'index.php?toast=no_auth';
            }else{
                toastr.remove();
                toastr.error(data_parsed.error.message, 'Ошибка!');
            }
            console.log(data_parsed.error.message);
            return true;
        }
        if(!empty(data_parsed.response))
        {
            console.log(data_parsed.response);
            return true;
        }
        if(!empty(data_parsed.block))
        {
            chat_list.innerHTML = '';
            chat_list.insertBefore(create(data_parsed.block.html), chat_list.childNodes[0]);
            console.log(data_parsed.block.message);
            return true;
        }
    });
}
$(document).ready(function(){
    updateChatList();
    if (window.matchMedia('(max-width: 767px)').matches)
    {
        document.getElementById('plist').setAttribute('hidden', '');
    }
});
function setChat(element)
{
    let id = element.id;
    let main_chat = document.getElementById('chat_main');
    if (main_chat.chat_id === id){
        return true;
    }
    $.post('src/php/handlers/support_handler.php', {'type': 'get_chat', 'sess_id': getCookie('sess_id'), 'chat_id': id}, function (data){
        const data_parsed = $.parseJSON(data);
        if(!empty(data_parsed.error))
        {
            if(data_parsed.error.code === 9909)
            {
                location.href = 'index.php?toast=no_auth';
            }else{
                toastr.remove();
                toastr.error(data_parsed.error.message, 'Ошибка!');
            }
            console.log(data_parsed.error.message);
            return true;
        }
        if(!empty(data_parsed.response))
        {
            console.log(data_parsed.response);
            return true;
        }
        if(!empty(data_parsed.block))
        {
            main_chat.innerHTML = '';
            main_chat.insertBefore(create(data_parsed.block.chat), main_chat.childNodes[0]);
            main_chat.chat_id = data_parsed.block.chat_id;
            document.getElementById('chat_history_id').insertBefore(create(data_parsed.block.messages), document.getElementById('chat_history_id').childNodes[0]);
            document.getElementById('chat_input_id').insertBefore(create(data_parsed.block.input), document.getElementById('chat_input_id').childNodes[0]);
            console.log(data_parsed.block.message);
            let input = document.querySelector('#chat_message');
            input.addEventListener('keydown', function (e){
                if (e.keyCode === 13)
                {
                    if (shift === true)
                    {
                        input.value = input.value + "\n";
                    }else{
                        enter = true;
                    }
                }else if (e.keyCode === 16) {
                    if (enter === true) {
                        input.value = input.value + "\n";
                        console.log('okey');
                    } else {
                        shift = true;
                    }
                }
            });
            input.addEventListener('keyup', function (e){
                if (e.keyCode === 13)
                {
                    if (shift === false)
                    {
                        sendMessage();
                    }
                    enter = false;
                }else if (e.keyCode === 16) {
                    shift = false;
                }
            });
            let chat_history = document.getElementById('chat_messages');
            chat_history.scrollTo({ left: 0, top: chat_history.scrollHeight });
            if (window.matchMedia('(max-width: 767px)').matches)
            {
                document.getElementById('plist').setAttribute('hidden', '');
            }
            return true;
        }
    });
}

function hideChat(){
    document.getElementById('plist').removeAttribute('hidden');
    document.getElementById('chat_main').chat_id = null;
    document.getElementById('chat_header').innerHTML = '';
    document.getElementById('chat_messages').innerHTML = '';
    let fd = document.getElementById('chat_input_id')
    if (fd !== null) fd.innerHTML = '';
}

function sendMessage()
{
    let input = document.getElementById('chat_message');
    if (!input.disabled)
    {
        if (!empty(input.value.trim()))
        {
            if (Math.floor(Date.now() / 1000) >= sleep)
            {
                let chat_id = document.getElementById('chat_main').chat_id;
                $.post('src/php/handlers/support_handler.php', {'type': 'send_message', 'sess_id': getCookie('sess_id'), 'chat_id': chat_id, 'message': input.value}, function(data){
                    const data_parsed = $.parseJSON(data);
                    if (!empty(data_parsed.block)){
                        let chat_history = document.getElementById('chat_history_id');
                        chat_history.insertBefore(create(data_parsed.block.html), chat_history.childNodes[-1]);
                        chat_history = document.getElementById('chat_messages');
                        chat_history.scrollTo({ left: 0, top: chat_history.scrollHeight, behavior: "smooth" });
                    }else{
                        toastr.remove();
                        toastr.error(data_parsed.error.message, 'Ошибка!');
                    }
                });
                sleep = Math.floor(Date.now() / 1000) + 5;
            }else{
                toastr.error('Подождите ещё ' + (sleep - Math.floor(Date.now() / 1000)) + ' сек.', 'Не так быстро!');
                toastr.remove();
            }
            input.value = '';
        }else{
            toastr.error('Вы не ввели текст!', 'Запрещено!');
            toastr.remove();
        }
    }else{
        toastr.error('Нельзя отправлять текст из выключеного поля!', 'Запрещено!');
        toastr.remove();
    }
}

function newChat()
{
    let main_chat = document.getElementById('chat_main');
    $.post('src/php/handlers/support_handler.php', {'type': 'create_chat', 'sess_id': getCookie('sess_id')}, function (data){
        const data_parsed = $.parseJSON(data);
        if(!empty(data_parsed.error))
        {
            if(data_parsed.error.code === 9909)
            {
                location.href = 'index.php?toast=no_auth';
            }else{
                toastr.remove();
                toastr.error(data_parsed.error.message, 'Ошибка!');
            }
            console.log(data_parsed.error.message);
            return true;
        }
        if(!empty(data_parsed.response))
        {
            console.log(data_parsed.response);
            return true;
        }
        if(!empty(data_parsed.block))
        {
            main_chat.innerHTML = '';
            main_chat.insertBefore(create(data_parsed.block.chat), main_chat.childNodes[0]);
            main_chat.chat_id = data_parsed.block.chat_id;
            document.getElementById('chat_history_id').insertBefore(create(data_parsed.block.messages), document.getElementById('chat_history_id').childNodes[0]);
            document.getElementById('chat_input_id').insertBefore(create(data_parsed.block.input), document.getElementById('chat_input_id').childNodes[0]);
            console.log(data_parsed.block.message);
            let input = document.querySelector('#chat_message');
            input.addEventListener('keydown', function (e){
                if (e.keyCode === 13)
                {
                    if (shift === true)
                    {
                        input.value = input.value + "\n";
                    }else{
                        enter = true;
                    }
                }else if (e.keyCode === 16) {
                    if (enter === true) {
                        input.value = input.value + "\n";
                        console.log('okey');
                    } else {
                        shift = true;
                    }
                }
            });
            input.addEventListener('keyup', function (e){
                if (e.keyCode === 13)
                {
                    if (shift === false)
                    {
                        sendMessage();
                    }
                    enter = false;
                }else if (e.keyCode === 16) {
                    shift = false;
                }
            });
            let chat_history = document.getElementById('chat_messages');
            chat_history.scrollTo({ left: 0, top: chat_history.scrollHeight });
            let chat_list = document.getElementById('chat_list');
            chat_list.innerHTML = '';
            chat_list.insertBefore(create(data_parsed.block.tickets), chat_list.childNodes[0]);
            return true;
        }
    });
}

function autoUpdate()
{
    if (document.hidden){
        console.log('hidden');
        return true;
    }
    let chat_id = document.getElementById('chat_main').chat_id;
    if (!empty(chat_id))
    {
        $.post('src/php/handlers/support_handler.php', {'type': 'get_new_message', 'sess_id': getCookie('sess_id'), 'chat_id': chat_id}, function (data){
            if (empty(data)){
                return true;
            }
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.block))
            {
                let ids = data_parsed.block.ids.split(',');
                for (let i = 0; i < ids.length; i++)
                {
                    let el = document.getElementById(ids[i]);
                    if (el instanceof HTMLElement)
                    {

                    }else{
                        if (empty(el))
                        {
                            let chat_history = document.getElementById('chat_history_id');
                            chat_history.insertBefore(create(data_parsed.block.html[i]), chat_history.childNodes[-1]);
                            chat_history = document.getElementById('chat_messages');
                            chat_history.scrollTo({ left: 0, top: chat_history.scrollHeight, behavior: "smooth" });
                        }
                    }
                }
                return true;
            }else if (!empty(data_parsed.error))
            {
                toastr.remove();
                toastr.error(data_parsed.error.message, 'Ошибка!');
                return true;
            }else{
                return true;
            }
        });
    }
}

setInterval(autoUpdate, 1000);