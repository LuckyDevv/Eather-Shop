no_auth = false;
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
}
$.ajaxSetup({ cache: false });
let header_;
let header_mobile_;
let search_;
let hgh = window.location.pathname.split('/');
function search_product()
{
    const param = document.getElementById('search_param').value.trim();
    if (!empty(param)) {
        location.href = 'search.php?find_name=' + param;
    }
}
function empty(mixed_var) {
    return (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || mixed_var === undefined || mixed_var.length === 0);
}
function getCookie(name) {
    let end;
    const dc = document.cookie;
    const prefix = name + "=";
    let begin = dc.indexOf("; " + prefix);
    if (begin === -1) {
        begin = dc.indexOf(prefix);
        if (begin !== 0) return null;
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
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.header)){
                header_ = data_parsed.header;
                document.body.insertBefore(create(header_), document.body.childNodes[0]);
                nextReady()
            }else{
                toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    }else{
        $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header_mobile'}, function(data){
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.header) && !empty(data_parsed.search)){
                header_mobile_ = data_parsed.header;
                search_ = data_parsed.search;
                document.body.insertBefore(create(search_), document.body.childNodes[0]);
                document.body.insertBefore(create(header_mobile_), document.body.childNodes[-1]);
                nextReady()
            }else{
                toastr.error('Не удалось загрузить модули, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    }
});
function nextReady()
{
    if (empty(getCookie('sess_id'))) {
        $.post('src/php/handlers/tpl_handler.php', {'type': 'get_modals'}, function(data){
            const data_parsed = $.parseJSON(data);
            if (!empty(data_parsed.auth) && !empty(data_parsed.reg)){
                document.body.insertBefore(create(data_parsed.auth), document.body.childNodes[0]);
                document.body.insertBefore(create(data_parsed.reg), document.body.childNodes[0]);
            }else{
                toastr.error('Не удалось загрузить модальные окна, перезагрузите страницу!', 'Критическая ошибка!');
            }
        });
    } else {
        let personal_btn = document.getElementById('personal-btn');
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
            toastr.error('Вы не авторизованы!', 'Ошибка!');
        }else{
            location.href = 'index.php';
        }
    }
    setActiveNav();
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
        var login = $('#login_input').val();
        var password = $('#password_input').val();
        if(empty(login)){
            toastr.error('Вы не ввели логин!', 'Ошибка!')
            return true;
        }
        if(empty(password)){
            toastr.error('Вы не ввели пароль!', 'Ошибка!')
            return true;
        }
        if(!empty(login) && !empty(password)) {
            $.post('src/php/handlers/auth.php', {'type': 'auth', 'login': login, 'password': password}, function(data){
                var data_parsed = $.parseJSON(data);
                if(empty(data_parsed.error)){
                    toastr.success('Вы успешно авторизовались!', 'Успех!');
                    document.cookie = "sess_id=" + data_parsed.response;
                    $('.btn-close')?.click();
                    document.getElementById('authModal')?.remove();
                    document.getElementById('regModal')?.remove();
                }else{
                    toastr.error(data_parsed.error.message, 'Ошибка!');
                }
            });
        }else toastr.error('Не введены требуемые данные!', 'Ошибка!');
    }else toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
}
function regButton(){
    if(empty(getCookie('sess_id')))
    {
        const login = $('#reg_login_input').val();
        const password = $('#reg_password_input').val();
        toastr.options.timeOut = 2000;
        if(empty(login)){
            toastr.error('Вы не ввели логин!', 'Ошибка!')
            return true;
        }
        if(empty(password)){
            toastr.error('Вы не ввели пароль!', 'Ошибка!')
            return true;
        }
        if(empty(password_confirm)){
            toastr.error('Вы не ввели подтверждение пароля!', 'Ошибка!')
            return true;
        }
        if(password !== password_confirm){
            toastr.error('Подтверждение пароля не совпадает с паролем!', 'Ошибка!')
            return true;
        }
        if(!empty(login) && !empty(password) && !empty(password_confirm)) {
            $.post('src/php/handlers/auth.php', {'type': 'registration', 'login': login, 'password': password, 'password_confirm': password_confirm}, function(data){
                const data_parsed = $.parseJSON(data);
                if(empty(data_parsed.error)){
                    toastr.success('Вы успешно зарегистрировались!', 'Успех!');
                    document.cookie = "sess_id=" + data_parsed.response;
                    $('.btn-close')?.click();
                    document.getElementById('authModal')?.remove();
                    document.getElementById('regModal')?.remove();
                }else{
                    toastr.error(data_parsed.error.message, 'Ошибка!');
                }
            });
        }else toastr.error('Не введены требуемые данные!', 'Ошибка!');
    }else toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
}
function supportBtn(){
    if (empty(getCookie('sess_id')))
    {
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
                const data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.header)){
                    header_ = data_parsed.header;
                }else{
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
            }
        }
    }else{
        if (empty(header_mobile_) && empty(search_)) {
            $.post('src/php/handlers/tpl_handler.php', {'type': 'get_header_mobile'}, function(data){
                const data_parsed = $.parseJSON(data);
                if (!empty(data_parsed.header) && !empty(data_parsed.search)){
                    header_mobile_ = data_parsed.header;
                    search_ = data_parsed.search;
                }else{
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
            }
        }
    }
}

function update_personal_button()
{
    if (!empty(getCookie('sess_id')))
    {
        let personal_btn = document.getElementById('personal-btn');
        if (!empty(personal_btn)) {
            personal_btn.removeAttribute('data-bs-toggle');
            personal_btn.removeAttribute('data-bs-target');
            personal_btn.removeAttribute('href');
            personal_btn.setAttribute('href', 'personal.php');
        }
    }
}

window.onresize = reportWindowSize;