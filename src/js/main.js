var change = false;
var no_auth = false;
var login = null; var password = null;
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "100",
    "hideDuration": "100",
    "timeOut": "2000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
$.ajaxSetup({ cache: false });
var header_;
var header_mobile_;
var search_;
var hgh = window.location.pathname.split('/');
function search_product()
{
    var param = document.getElementById('search_param').value.trim();
    if (!empty(param)) {
        location.href = 'search.php?find_name=' + param;
    }
}
function empty(mixed_var) {
    return (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || mixed_var === undefined || mixed_var.length === 0);
}
function getCookie(name) {
    var end;
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin === -1) {
        begin = dc.indexOf(prefix);
        if (begin !== 0){
            return null;
        }
    }
    else
    {
        begin += 2;
        end = document.cookie.indexOf(";", begin);
        if (end === -1) {
            end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}
function create(htmlStr) {
    var frag = document.createDocumentFragment(),
        temp = document.createElement('div');
    temp.innerHTML = htmlStr;
    while (temp.firstChild) {
        frag.appendChild(temp.firstChild);
    }
    return frag;
}
$(document).ready(function(){
    if (window.innerWidth > 1000) {
        $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header'}, function(data){
            var data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.header)){
                header_ = data_parsed.header;
                document.body.insertBefore(create(header_), document.body.childNodes[0]);
                nextReady();
            }else{
                toastr.remove();
                toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    }else{
        $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header_mobile'}, function(data){
            var data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.header) && !empty(data_parsed.search)){
                header_mobile_ = data_parsed.header;
                search_ = data_parsed.search;
                document.body.insertBefore(create(search_), document.body.childNodes[0]);
                document.body.insertBefore(create(header_mobile_), document.body.childNodes[-1]);
                nextReady();
            }else{
                toastr.remove();
                toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    }
});
function nextReady()
{
    if (empty(getCookie('sess_id'))) {
        $.post('src/php/handlers/tpl_handler.php', {'type': 'get_modals'}, function(data){
            var data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.auth) && !empty(data_parsed.reg)){
                document.body.insertBefore(create(data_parsed.auth), document.body.childNodes[0]);
                document.body.insertBefore(create(data_parsed.reg), document.body.childNodes[0]);
            }else{
                toastr.remove();
                toastr.error('Не удалось загрузить модальные окна, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    } else {
        var personal_btn = document.getElementById('personal-btn');
        if (!empty(personal_btn)) {
            personal_btn.removeAttribute('data-bs-toggle');
            personal_btn.removeAttribute('data-bs-target');
            personal_btn.removeAttribute('href');
            personal_btn.setAttribute('href', 'personal.php');
        }
    }
    if(no_auth === true)
    {
        if(empty(getCookie('sess_id')))
        {
            toastr.remove();
            toastr.error('Вы не авторизованы!', 'Ошибка!');
        }else{
            location.href = 'index.php';
        }
    }
    setActiveNav();
    changeIcons();
}
function changeIcons()
{
    if (change === true)
    {
        document.getElementById('ordersEl')?.remove();
        document.getElementById('cartEl')?.remove();
        document.getElementById('favoritesEl')?.remove();
        document.getElementById('supportEl')?.remove();
        var prsTxt = document.getElementById('personalText');
        if (prsTxt !== null){  prsTxt.innerHTML = 'Войти';  }
        var prsIcon = document.getElementById('personalIcon');
        if (prsIcon !== null){
            prsIcon.classList.replace('bi-person-circle', 'bi-box-arrow-in-right');
        }
        prsTxt = null;
    }
}
function setActiveNav()
{

    switch (hgh[hgh.length - 1]) {
        case 'index.php':
            document.getElementById('mainButton')?.classList.add('nav-item-active');
            break;
        case 'personal.php':
            document.getElementById('personal-btn')?.classList.add('nav-item-active');
            break;
        case 'support.php':
            document.getElementById('supportButton')?.classList.add('nav-item-active');
            break;
    }
}
function eye_Auth(){
    var one = document.getElementById("password_input");
    if (one.type === "password") {
        one.type = "text";
    } else {
        one.type = "password";
    }
}
function eye_Reg() {
    var one = document.getElementById("reg_password_2step_input");
    if (one.type === "password") {
        one.type = "text";
    } else {
        one.type = "password";
    }
    var two = document.getElementById("reg_password_input");
    if (two.type === "password") {
        two.type = "text";
    } else {
        two.type = "password";
    }
}
function authButton(){
    if(empty(getCookie('sess_id')))
    {
        var login_ = $('#login_input').val();
        var password_ = $('#password_input').val();
        if(empty(login_)){
            toastr.remove();
            toastr.error('Вы не ввели логин!', 'Ошибка!')
            return true;
        }
        if(empty(password_)){
            toastr.remove();
            toastr.error('Вы не ввели пароль!', 'Ошибка!')
            return true;
        }
        if(!empty(login_) && !empty(password_)) {
            login = login_;
            password = password_;
            $.post('src/php/handlers/auth.php', {'type': 'auth', 'login': login_, 'password': password_}, function(data){
                var data_parsed = $.parseJSON(data);
                if(!empty(data_parsed.response)){
                    if (data_parsed.response === '2fa')
                    {
                        $('#regModal').modal('hide');
                        $('#authModal').modal('hide');
                        $('#2faModalConfirm').modal('show');
                    }else{
                        toastr.remove();
                        toastr.success('Вы успешно авторизовались!', 'Успех!');
                        document.cookie = "sess_id=" + data_parsed.response;
                        $('.btn-close')?.click();
                        document.getElementById('authModal')?.remove();
                        document.getElementById('regModal')?.remove();
                        setTimeout(function (){
                            location.reload();
                        }, 900);
                    }
                }else{
                    toastr.remove();
                    toastr.error(data_parsed.error.message, 'Ошибка!');
                }
            });
        }else{
            toastr.remove();
            toastr.error('Не введены требуемые данные!', 'Ошибка!');
        }
    }else{
        toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
        toastr.remove();
    }
}

$('#two_fa_submit_confirm').click(function (){
    let num1_confirm = document.getElementById('2fa_num1_confirm');
    let num2_confirm = document.getElementById('2fa_num2_confirm');
    let num3_confirm = document.getElementById('2fa_num3_confirm');
    let num4_confirm = document.getElementById('2fa_num4_confirm');
    let num5_confirm = document.getElementById('2fa_num5_confirm');
    let num6_confirm = document.getElementById('2fa_num6_confirm');
    if (
        !empty(num1_confirm) && !empty(num2_confirm) && !empty(num3_confirm) && !empty(num4_confirm) && !empty(num5_confirm)
        && !empty(num6_confirm) && !empty(num1_confirm.value) && !empty(num2_confirm.value) && !empty(num3_confirm.value)
        && !empty(num4_confirm.value) && !empty(num5_confirm.value) && !empty(num6_confirm.value)
        && !empty(password) && !empty(login)
    )
    {
        let confirm_code = '' + num1_confirm.value + num2_confirm.value + num3_confirm.value + num4_confirm.value + num5_confirm.value + num6_confirm.value + '';
        $.post('src/php/handlers/auth.php', {'type': '2fa_auth', 'login': login, 'password': password, 'code': confirm_code},
            function (data){
                const data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.response))
                {
                    toastr.remove();
                    toastr.success('Вы успешно авторизовались!', 'Успех!');
                    document.cookie = "sess_id=" + data_parsed.response;
                    $('.btn-close')?.click();
                    document.getElementById('authModal')?.remove();
                    document.getElementById('regModal')?.remove();
                    setTimeout(function (){
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

function regButton(){
    if(empty(getCookie('sess_id')))
    {
        var login = $('#reg_login_input').val();
        var password = $('#reg_password_input').val();
        var password_confirm = $('#reg_password_2step_input').val();
        toastr.options.timeOut = 2000;
        if(empty(login)){
            toastr.remove();
            toastr.error('Вы не ввели логин!', 'Ошибка!')
            return true;
        }
        if(empty(password)){
            toastr.remove();
            toastr.error('Вы не ввели пароль!', 'Ошибка!')
            return true;
        }
        if(empty(password_confirm)){
            toastr.remove();
            toastr.error('Вы не ввели подтверждение пароля!', 'Ошибка!')
            return true;
        }
        if(password !== password_confirm){
            toastr.remove();
            toastr.error('Подтверждение пароля не совпадает с паролем!', 'Ошибка!')
            return true;
        }
        if(!empty(login) && !empty(password) && !empty(password_confirm)) {
            $.post('src/php/handlers/auth.php', {'type': 'registration', 'login': login, 'password': password, 'password_confirm': password_confirm}, function(data){
                const data_parsed = $.parseJSON(data);
                if(empty(data_parsed.error)){
                    toastr.remove();
                    toastr.success('Вы успешно зарегистрировались!', 'Успех!');
                    document.cookie = "sess_id=" + data_parsed.response;
                    $('.btn-close')?.click();
                    document.getElementById('authModal')?.remove();
                    document.getElementById('regModal')?.remove();
                }else{
                    toastr.remove();
                    toastr.error(data_parsed.error.message, 'Ошибка!');
                }
            });
        }else{
            toastr.error('Не введены требуемые данные!', 'Ошибка!');
            toastr.remove();
        }
    }else{
        toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
        toastr.remove();
    }
}
function supportBtn(){
    if (empty(getCookie('sess_id')))
    {
        toastr.remove();
        toastr.error('Вы не авторизованы!', 'Ошибка!');
    }else{
        location.href = 'support.php';
    }
}
$('.input-search').focus(function () {
    $('.button-search').css('box-shadow', '0 0 7px 2px #198754');
}).blur(function () {
    $('.button-search').css('box-shadow', '');
});

function reportWindowSize() {
    if (window.innerWidth > 1000) {
        if (empty(header_)){
            $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header'}, function(data){
                var data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.header)){
                    header_ = data_parsed.header;
                }else{
                    toastr.remove();
                    toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
                }
            });
        }else{
            if (empty(document.getElementById('nav')))
            {
                document.querySelectorAll('#nav_mobile').forEach((item) => {
                    item.remove();
                });
                document.body.insertBefore(create(header_), document.body.childNodes[0]);
                update_personal_button();
                changeIcons();
            }
        }
    }else{
        if (empty(header_mobile_) && empty(search_)) {
            $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header_mobile'}, function(data){
                var data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.header) && !empty(data_parsed.search)){
                    header_mobile_ = data_parsed.header;
                    search_ = data_parsed.search;
                }else{
                    toastr.remove();
                    toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
                }
            });
        }else{
            if (empty(document.getElementById('nav_mobile'))) {
                document.querySelectorAll('#nav').forEach((item) => {
                    item.remove();
                });
                document.body.insertBefore(create(search_), document.body.childNodes[0]);
                document.body.insertBefore(create(header_mobile_), document.body.childNodes[-1]);
                setActiveNav();
                update_personal_button();
                changeIcons();
            }
        }
    }
}

function update_personal_button()
{
    if (!empty(getCookie('sess_id')))
    {
        var personal_btn = document.getElementById('personal-btn');
        if (!empty(personal_btn)) {
            personal_btn.removeAttribute('data-bs-toggle');
            personal_btn.removeAttribute('data-bs-target');
            personal_btn.removeAttribute('href');
            personal_btn.setAttribute('href', 'personal.php');
        }
    }
}

window.onresize = reportWindowSize;

// OTP
const inputs_confirm = document.querySelectorAll(".otp-input-confirm");
const button_confirm = document.querySelector("#two_fa_submit_confirm");
window.addEventListener("load", () => inputs_confirm[0].focus());
button_confirm.setAttribute("disabled", "disabled");
inputs_confirm[0].addEventListener("paste", function (event) {
    event.preventDefault();
    const pastedValue = (event.clipboardData || window.clipboardData).getData("text");
    const otpLength = inputs_confirm.length;
    for (let i = 0; i < otpLength; i++) {
        if (i < pastedValue.length) {
            inputs_confirm[i].value = pastedValue[i];
            inputs_confirm[i].removeAttribute("disabled");
            inputs_confirm[i].focus;
        } else {
            inputs_confirm[i].value = ""; // Clear any remaining inputs
            inputs_confirm[i].focus;
        }
    }
});
inputs_confirm.forEach((input, index1) => {
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
            inputs_confirm.forEach((input, index2) => {
                if (index1 <= index2 && prevInput) {
                    input.setAttribute("disabled", true);
                    input.value = "";
                    prevInput.focus();
                }
            });
        }
        button_confirm.classList.remove("active");
        button_confirm.setAttribute("disabled", "disabled");
        const inputsNo = inputs_confirm.length;
        if (!inputs_confirm[inputsNo - 1].disabled && inputs_confirm[inputsNo - 1].value !== "") {
            button_confirm.classList.add("active");
            button_confirm.removeAttribute("disabled");
            return null;
        }
    });
});