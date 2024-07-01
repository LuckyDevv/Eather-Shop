function empty(mixed_var) {
    return (mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || mixed_var === undefined || mixed_var.length === 0);
}
function deleteFavorite(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'delete_favorite', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        const data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.info('Товар удалён из избранного!');
            const id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="add_' + product_id + '" class="btn heart" onclick="addFavorite(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Добавить в избранное">\n' +
                    '<h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}
function addFavorite(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'add_favorite', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        const data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.info('Товар добавлен в избранное!');
            const id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="delete_' + product_id + '" class="btn unheart" onclick="deleteFavorite(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Удалить из избранного">\n' +
                    '<h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}
function deleteCart(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'delete_cart', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        const data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.info('Товар удалён из корзины!');
            const id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="add-cart_' + product_id + '" class="btn" onclick="addCart(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Добавить в корзину">\n' +
                    '<h4><i class="bi bi-cart-plus-fill" style="color: #1b6eb9; -webkit-text-stroke: 1px #000000"></i></h4>\n' +
                    '</button>'
            }
        }else{
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}
function addCart(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'add_cart', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        const data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.info('Товар добавлен в корзину!');
            const id_el = element.getAttribute('id');
            let lines = document.querySelectorAll('#' + id_el);
            for  (let line of lines) {
                line.outerHTML = '<button id="delete-cart_' + product_id + '" class="btn" onclick="deleteCart(' + acc_id + ', ' + product_id + ', this)" data-bs-toggle="tooltip" title="Удалить из корзину">\n' +
                    '<h4><i class="bi bi-cart-x-fill" style="color: #23d214; -webkit-text-stroke: 1px #000000"></i></h4>\n' +
                    '</button>';
            }
        }else{
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}
$('a.a_product').click(function (e) {
    if (e.target.localName === 'i')
    {
        e.preventDefault()
    }
});
$('.block-favorite').click(function (e){
    location.href = 'favorites.php';
});

$('.block-cart').click(function (e){
    location.href = 'cart.php';
});