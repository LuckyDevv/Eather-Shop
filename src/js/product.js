function add_to_cart(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'add_cart', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        const data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.remove();
            toastr.info('Товар добавлен в корзину!');
            element.outerHTML = '<button type="button" onclick="del_from_cart(' + acc_id + ', ' + product_id + ', this)" style="background: #00bb0e; border: 1px solid #00bb0e;" class="btn btn-primary buy-button">Удалить из корзины</button>\n';
        }else{
            toastr.remove();
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}
function del_from_cart(acc_id, product_id, element)
{
    $.post('src/php/handlers/personal_handler.php', {'type': 'delete_cart', 'acc_id': acc_id, 'product_id': product_id}, function(data){
        var data_parsed = $.parseJSON(data);
        if(empty(data_parsed.error)){
            toastr.remove();
            toastr.info('Товар удалён из корзины!');
            element.outerHTML = '<button type="button" onclick="add_to_cart(' + acc_id + ', ' + product_id + ', this)" style="background: #0d6efd" class="btn btn-primary buy-button">Добавить в корзину</button>\n';
        }else{
            toastr.remove();
            toastr.error(data_parsed.error.message, 'Ошибка!');
        }
    });
}