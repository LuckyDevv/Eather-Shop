let pass_hidden = true; let old_avatar = null; let secret_code = null; let qr_2fa = null;
let all = {
    'login': document.getElementById('login').value,
    'username': document.getElementById('name').value,
}

window.addEventListener("beforeunload", function () {
    if (!empty(secret_code)) {
        $.post('src/php/handlers/2fa_handler.php', {'type': 'delete_secret', 'sess_id': getCookie('sess_id'), 'secret': secret_code});
    }
});

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
                toastr.error(data_parsed.error.message, 'Ошибка!');
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

$('#enable-2fa').click(function(){
    if (empty(secret_code) || empty(qr_2fa))
    {
        $.post('src/php/handlers/2fa_handler.php', {'type': 'generate', 'sess_id': getCookie('sess_id')}, function (data){
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.response)) {
                let qr_img = document.getElementById('2fa_qr');
                if (!empty(qr_img)) {
                    secret_code = data_parsed.response.secret_code;
                    qr_2fa = data_parsed.response.img;
                    setTimeout(function(){
                        qr_img.src = qr_2fa;
                        qr_img.classList.add('qr-loaded');

                        let secret_code_ = document.getElementById('secret_code_');
                        if (!empty(secret_code_))
                        {
                            secret_code_.innerHTML = secret_code;
                        }
                    }, 500);
                    return true;
                }
                if (!empty(data_parsed.error)) {
                    toastr.remove();
                    toastr.error(data_parsed.error.message, 'Ошибка!');
                    return true;
                }
            }
        });
    }
});

$('#two_fa_submit').click(function (){
   let num1 = document.getElementById('2fa_num1');
   let num2 = document.getElementById('2fa_num2');
   let num3 = document.getElementById('2fa_num3');
   let num4 = document.getElementById('2fa_num4');
   let num5 = document.getElementById('2fa_num5');
   let num6 = document.getElementById('2fa_num6');
   if (
       !empty(num1) && !empty(num2) && !empty(num3) && !empty(num4) && !empty(num5) && !empty(num6) &&
       !empty(num1.value) && !empty(num2.value) && !empty(num3.value) && !empty(num4.value) && !empty(num5.value) && !empty(num6.value)
   )
   {
       let confirm_code = '' + num1.value + num2.value + num3.value + num4.value + num5.value + num6.value + '';
       $.post('src/php/handlers/2fa_handler.php', {'type': 'confirm', 'secret_code': secret_code, 'confirm_code': confirm_code, 'sess_id': getCookie('sess_id')},
       function (data){
           const data_parsed = $.parseJSON(data);
           if (!empty(data_parsed.response))
           {
               toastr.remove();
               toastr.success(data_parsed.response);
               setTimeout(function(){
                   location.reload();
               }, 900);
               return true;
           }
           if (!empty(data_parsed.error))
           {
               toastr.remove();
               toastr.error(data_parsed.error.message, 'Ошибка!');
               return true;
           }
       });
   }else{
       toastr.remove();
       toastr.error('Ошибка!', 'Не введён код!');
   }
});

$('#disable-2fa').click(function (){
   if (window.confirm('Вы точно хотите отключить 2FA?'))
   {
       $.post('src/php/handlers/2fa_handler.php', {'type': 'disable', 'sess_id': getCookie('sess_id')}, function (data){
           const data_parsed = $.parseJSON(data);
           if (!empty(data_parsed.response))
           {
               toastr.remove();
               toastr.success('Вы успешно отключили 2FA');
               setTimeout(function(){
                   location.reload();
               }, 900);
               return true;
           }
           if (!empty(data_parsed.error))
           {
               toastr.remove();
               toastr.error(data_parsed.error.message, 'Ошибка!');
               return true;
           }
       });
   }
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
                    toastr.error(data_parsed.error.message, 'Ошибка!');
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


// OTP
const inputs = document.querySelectorAll(".otp-input");
const button = document.querySelector("#two_fa_submit");
window.addEventListener("load", () => inputs[0].focus());
button.setAttribute("disabled", "disabled");
inputs[0].addEventListener("paste", function (event) {
    event.preventDefault();
    const pastedValue = (event.clipboardData || window.clipboardData).getData("text");
    const otpLength = inputs.length;
    for (let i = 0; i < otpLength; i++) {
        if (i < pastedValue.length) {
            inputs[i].value = pastedValue[i];
            inputs[i].removeAttribute("disabled");
            inputs[i].focus;
        } else {
            inputs[i].value = ""; // Clear any remaining inputs
            inputs[i].focus;
        }
    }
});
inputs.forEach((input, index1) => {
    input.addEventListener("keyup", (e) => {
        const currentInput = input;
        const nextInput = input.nextElementSibling;
        const prevInput = input.previousElementSibling;
        if (currentInput.value.length > 1) {
            currentInput.value = "";
            return;
        }
        if (nextInput && nextInput.hasAttribute("disabled") && currentInput.value !== "") {
            nextInput.removeAttribute("disabled");
            nextInput.focus();
        }
        if (e.key === "Backspace") {
            inputs.forEach((input, index2) => {
                if (index1 <= index2 && prevInput) {
                    input.setAttribute("disabled", true);
                    input.value = "";
                    prevInput.focus();
                }
            });
        }
        button.classList.remove("active");
        button.setAttribute("disabled", "disabled");
        const inputsNo = inputs.length;
        if (!inputs[inputsNo - 1].disabled && inputs[inputsNo - 1].value !== "") {
            button.classList.add("active");
            button.removeAttribute("disabled");
            return null;
        }
    });
});