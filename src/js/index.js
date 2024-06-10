no_auth = false;
$(function(){
    // инициализации подсказок для всех элементов на странице, имеющих атрибут data-toggle="tooltip"
    $('[data-toggle="tooltip"]').tooltip();
});
function empty(mixed_var) {
    return (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || mixed_var === undefined || mixed_var.length === 0);
}
function vtik()
{
    const param = document.getElementById('search_param').value;
    location.href = 'search.php?find_name=' + param;
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
function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    // because unescape has been deprecated, replaced with decodeURI
    //return unescape(dc.substring(begin + prefix.length, end));
    return decodeURI(dc.substring(begin + prefix.length, end));
}
$(document).ready(function(){
    let pernsonal_btn;
    if (empty(getCookie('sess_id'))) {
        var fragment = create('<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">\n' +
            '  <div class="modal-dialog modal-dialog-centered">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title" id="authModalLabel">Вход в аккаунт</h5>\n' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">\n' +
            '        <form>\n' +
            '          <div class="mb-3">\n' +
            '            <label for="login_input" class="col-form-label">Логин:</label>\n' +
            '            <input type="text" class="form-control" id="login_input">\n' +
            '          </div>\n' +
            '          <div class="mb-3">\n' +
            '            <label for="password_input" class="col-form-label">Пароль:</label>\n' +
            '            <input type="password" class="form-control" id="password_input">\n' +
            '            <i class="bi bi-eye" style="font-size:24px; cursor: pointer" onclick="eye_Auth()"></i>\n ' +
            '          </div>\n' +
            '        </form>\n' +
            '      </div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>\n' +
            '        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#regModal">Зарегестрироваться</button>\n' +
            '        <button type="button" class="btn btn-primary" onclick="authButton()">Войти</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>');
// You can use native DOM methods to insert the fragment:
        document.body.insertBefore(fragment, document.body.childNodes[0]);

        var fragment = create('<div class="modal fade" id="regModal" tabindex="-1" aria-labelledby="regModalLabel" aria-hidden="true">\n' +
            '  <div class="modal-dialog modal-dialog-centered">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title" id="regModalLabel">Регистрация</h5>\n' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">\n' +
            '        <form>\n' +
            '          <div class="mb-3">\n' +
            '            <label for="reg_login_input" class="col-form-label">Логин:</label>\n' +
            '            <input type="text" class="form-control" id="reg_login_input">\n' +
            '          </div>\n' +
            '          <div class="mb-3">\n' +
            '            <label for="reg_password_input" class="col-form-label">Пароль:</label>\n' +
            '            <input type="password" class="form-control" id="reg_password_input">\n' +
            '          </div>\n' +
            '          <div class="mb-3">\n' +
            '            <label for="reg_password_2step_input-text" class="col-form-label">Подтверждение пароля:\n</label>' +
            '            <input type="password" class="form-control" id="reg_password_2step_input">\n' +
            '            <i class="bi bi-eye" style="font-size:24px; cursor: pointer" onclick="eye_Reg()"></i>\n ' +
            '          </div>\n' +
            '        </form>\n' +
            '      </div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>\n' +
            '        <button type="button" id="reg-btn" onclick="regButton()" class="btn btn-primary">Войти</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>');
// You can use native DOM methods to insert the fragment:
        document.body.insertBefore(fragment, document.body.childNodes[0]);
    } else {
        let personal_btn = document.getElementById('personal-btn');
        personal_btn.removeAttribute('data-bs-toggle');
        personal_btn.removeAttribute('data-bs-target');
        personal_btn.removeAttribute('href');
        personal_btn.setAttribute('href', 'personal.php')
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
});
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
            $.post('src/php/auth.php', {'type': 'auth', 'login': login, 'password': password}, function(response){
                var response = $.parseJSON(response);
                if(empty(response.error)){
                    toastr.success('Вы успешно авторизовались!', 'Успех!');
                    document.cookie = "sess_id=" + response.response;
                    setTimeout(function href(){
                        location.href = 'personal.php';
                    }, 1000);
                }else{
                    toastr.error(response.error.message, 'Ошибка!');
                }
            });
        }else toastr.error('Не введены требуемые данные!', 'Ошибка!');
    }else toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
}
function regButton(){
    if(empty(getCookie('sess_id')))
    {
        var login = $('#reg_login_input').val();
        var password = $('#reg_password_input').val();
        var password_confirm = $('#reg_password_2step_input').val();
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
            $.post('src/php/auth.php', {'type': 'registration', 'login': login, 'password': password, 'password_confirm': password_confirm}, function(response){
                var response = $.parseJSON(response);
                if(empty(response.error)){
                    toastr.success('Вы успешно зарегистрировались!', 'Успех!');
                    document.cookie = "sess_id=" + response.response;
                    setTimeout(function href(){
                        location.href = 'personal.php';
                    }, 1000);
                }else{
                    toastr.error(response.error.message, 'Ошибка!');
                }
            });
        }else toastr.error('Не введены требуемые данные!', 'Ошибка!');
    }else toastr.error('Вы уже вошли в аккаунт!', 'Ошибка!');
}
function deleteFavorite(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'delete_favorite', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар удалён из избранного!');
            var id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="add_' + product_id + '" class="btn heart" onclick="addFavorite(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Добавить в избранное">\n' +
                    '<h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}
function addFavorite(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'add_favorite', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар добавлен в избранное!');
            var id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="delete_' + product_id + '" class="btn unheart" onclick="deleteFavorite(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Удалить из избранного">\n' +
                    '<h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}
function deleteCart(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'delete_cart', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар удалён из корзины!');
            var id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="add-cart_' + product_id + '" class="btn" onclick="addCart(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Добавить в корзину">\n' +
                    '<h4><i class="bi bi-cart-plus-fill" style="color: #1b6eb9; -webkit-text-stroke: 1px #000000"></i></h4>\n' +
                    '</button>'
            }
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}
function addCart(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'add_cart', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар добавлен в корзину!');
            var id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="delete-cart_' + product_id + '" class="btn" onclick="deleteCart(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Удалить из корзину">\n' +
                    '<h4><i class="bi bi-cart-x-fill" style="color: #23d214; -webkit-text-stroke: 1px #000000"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}
function add_to_cart(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'add_cart', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар добавлен в корзину!');
            element.outerHTML = '<button type="button" onclick="del_from_cart(' + acc_id + ', ' + product_id + ', this)" style="background: #00bb0e" class="btn btn-primary buy-button">Удалить из корзины</button>\n';
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}
function del_from_cart(acc_id, product_id, element)
{
    $.post('src/php/personal_handler.php', {'type': 'delete_cart', 'acc_id': acc_id, 'product_id': product_id}, function(response){
        var response = $.parseJSON(response);
        if(empty(response.error)){
            toastr.info('Товар удалён из корзины!');
            element.outerHTML = '<button type="button" onclick="add_to_cart(' + acc_id + ', ' + product_id + ', this)" style="background: #0d6efd" class="btn btn-primary buy-button">Добавить в корзину</button>\n';
        }else{
            toastr.error(response.error.message, 'Ошибка!');
        }
    });
}