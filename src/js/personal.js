let pass_hidden = true; let old_avatar = null;
let all = {
    'login': document.getElementById('login').value,
    'username': document.getElementById('name').value,
}

$('#view-pass').click(function (){
    let pass = document.getElementById('password_1');
    let pass_confirm = document.getElementById('password_2');
    if (!empty(pass)) {
        if (pass_hidden) {
            pass.type = 'text';
        }else{
            pass.type = 'password';
        }
    }
    if (!empty(pass_confirm)) {
        if (pass_hidden) {
            pass_confirm.type = 'text';
        }else{
            pass_confirm.type = 'password';
        }
    }
    pass_hidden = !pass_hidden;
});

$('#saveChanges').click(function (){
    let send = {
        'login': false,
        'username': false,
        'password': false
    }
    let login = document.getElementById('login').value;
    let username = document.getElementById('name').value;
    if (all.login !== login) {
        send.login = true;
    }
    if (all.username !== username) {
        send.username = true;
    }
    let pass = document.getElementById('password_1');
    let pass_confirm = document.getElementById('password_2');
    if (pass.value !== '' && pass_confirm.value !== ''){
        send.password = true;
    }

    let finish_send = {}
    if (send.login) {
        finish_send.login = login;
    }
    if (send.username) {
        finish_send.username = username;
    }
    if (send.password) {
        finish_send.password = pass.value;
        finish_send.password_confirm = pass_confirm.value;
    }

    if (!empty(finish_send.login) || !empty(finish_send.username) || !empty(finish_send.password)) {
        $.post('src/php/handlers/account_handler.php', {'type': 'set', 'source': finish_send, 'sess_id': getCookie('sess_id')}, function(data) {
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.response)) {
                toastr.remove();
                toastr.success(data_parsed.response);
                return true;
            }
            if (!empty(data_parsed.warning)) {
                toastr.remove();
                toastr.warning(data_parsed.warning, 'Внимание!');
                return true;
            }
            if (!empty(data_parsed.error)) {
                toastr.remove();
                toastr.error('Ошибка!', data_parsed.error.message);
                return true;
            }
        });
    }else{
        toastr.remove();
        toastr.warning('Не было внесено изменений!', 'Внимание!');
    }
});

$('#quitAccount').click(function (){
   $.post('src/php/handlers/account_handler.php', {'type': 'quit', 'sess_id': getCookie('sess_id')}, function (){
       location.reload();
   });
});

$('#upload_image_button').click(function (){
    try {
        var file_data = $('#frame').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('sess_id', getCookie('sess_id'));
        $.ajax({
            url: 'src/php/handlers/account_handler.php', // <-- point to server-side PHP script
            dataType: 'text',  // <-- what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(data){
                var data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.response)) {
                    toastr.remove();
                    toastr.success('Успешно!');
                    return true;
                }
                if (!empty(data_parsed.error))
                {
                    toastr.remove();
                    toastr.error('Ошибка!', data_parsed.error.message);
                    return true;
                }
            }
        });
    }catch (e) {}
});

$('#clear_image_button').click(function(){
    document.getElementById('formFile').value = null;
    document.getElementById('frame').src = 'photos/no-img.jpg';
    document.getElementById('avatar').src = 'photos/no-img.jpg';
});

$(document).ready(function(){
    document.getElementById('frame').src = document.getElementById('avatar').src;
});

function preview() {
    const object = URL.createObjectURL(event.target.files[0]);
    document.getElementById('frame').src = object;
    document.getElementById('frame').files = event.target.files;
    document.getElementById('avatar').src = object;
}
