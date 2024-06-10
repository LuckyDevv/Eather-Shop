<?php
require 'src/php/ProductsDB.php';
require 'src/php/PersonalDB.php';
require 'src/php/ConfigController.php';
require 'src/php/AccountsDB.php';
require 'src/php/Formatter.php';
$formatter = new Formatter();
$products_db = new ProductsDB('src/php/database/Products_DB.db');
$product_id = $_GET['product_id'] ?? null;
$find = false;
$acc_id = $formatter->get_cookie_acc_id($_COOKIE);
if(is_numeric($product_id)) {
    $product = $products_db->product_find_by_id((int) $product_id);
    if($product !== false) {
        $find = true;
        $name = $product['product_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <link href="src/css/bootstrap.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/product.css" rel="stylesheet">
    <link href="src/css/gallery.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <?php
    if($find)
    {
        echo '<title>'.$name.' - купить в интернет-магазина EATHER</title>';
    }else{
        echo '<title>Не удалось найти товар.</title>';
    }
    ?>
</head>
<body>
<?php echo $formatter->get_header(); ?>
<div class="container-fluid p-2 flex-block">
    <?php
    if($find)
    {
        $price = $product['product_price'];
        $old_price = $product['product_old_price'];
        $photo = $product['product_photo'];
        echo '<div class="container-md d-inline styled-block-2">
                <div class="C-carousel styled-block-2">';
        $photos = explode(',', $photo);
        if (count($photos) >= 1) {
            foreach($photos as $photo) {
                if (!is_file('photos/'.$photo)) continue;
                echo '<img src="photos/'.$photo.'" class="C-slide img-thumbnail" style="max-height: 600px; max-width: 600px; object-fit: cover; border-radius: 15px" alt="">';
            }
        }else{
            echo '<img src="photos/no-img.jpg" class="C-slide img-thumbnail gallery-image" alt="">';
        }
        echo '</div>';

        echo '</div>';
        $feedbacks = 0;
        $feedback_rate = ($feedbacks > 0 ? 5 / $feedbacks : 0);
        if ($feedback_rate >= 4) {
            $feedback_text = '<h6 style="color:#858983;">
                                <i class="bi bi-star-fill star-class"></i>
                                <t class="star-class">'.$feedback_rate.'</t> · '.$feedbacks.' отзывов · 
                                <i class="bi bi-truck"></i> '.$product['product_sales'].' покупок
                              </h6>';
        }elseif($feedback_rate >= 3 && $feedback_rate <= 3.9){
            $feedback_text = '<h6 style="color:#858983;">
                                <i class="bi bi-star-half star-class"></i>
                                <t class="star-class">'.$feedback_rate.'</t> · '.$feedbacks.' отзывов · 
                                <i class="bi bi-truck"></i> '.$product['product_sales'].' покупок
                              </h6>';
        }else{
            $feedback_text = '<h6 style="color:#858983;">
                                <i class="bi bi-star-fill star-class-bad"></i>
                                '.$feedback_rate.' · '.$feedbacks.' отзывов · 
                                <i class="bi bi-truck"></i> '.$product['product_sales'].' покупок
                              </h6>';
        }
        echo '<div class="container-md d-inline p-2 styled-block-1"><h1 >'.$name.'</h1>
              '.$feedback_text.'
              <p style="font-size: 20px">'.$product['product_description'].'</p>  </div>';
        $format_price = number_format($price, 2, ',', ' ');
        $amount = $product['product_amount'];
        if ($amount > 0) {
            $amount_text = '<div class="center-text text-delivery"></div>';
            if ($acc_id !== false)
            {
                if ((new PersonalDB('src/php/database/Personal_DB.db'))->cart_exists_product($acc_id, $product_id))
                {
                    $buy_button = '<button type="button" onclick="add_to_cart('.$acc_id.', '.$product_id.', this)" style="background: #0d6efd" class="btn btn-primary buy-button">Добавить в корзину</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
                }else{
                    $buy_button = '<button type="button" onclick="del_from_cart('.$acc_id.', '.$product_id.', this)" style="background: #00bb0e" class="btn btn-primary buy-button">Удалить из корзины</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
                }
            }else{
                $buy_button = '<button type="button" class="btn btn-primary buy-button">Добавить в корзину</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
            }
            $buy_one_click_button = '<button type="button" class="btn btn-info buy-one-click-button">Купить в один клик</button>';
        }else{
            $amount_text = '';
            $buy_button = '<button type="button" class="btn btn-primary buy-button disabled">Товар закончился</button>';
            $buy_one_click_button = '';
        }
        if($old_price !== 0){
            $discount = round((($old_price - $price) / $old_price) * 100);
            $discount_text = $discount >= 1 ? '<t style="color: red; font-weight: bolder">-'.$discount.'%</t>' : null;
            $format_old_price = number_format($old_price, 2, ',', ' ');
            if($discount < 0){
                $change_text = '<i class="bi bi-graph-up-arrow" style="color: red"></i> Цена повысилась';
            }else{
                $change_text = '<i class="bi bi-graph-down-arrow" style="color: #00bb0e;"></i> Цена понизилась';
            }
            echo '<div class="container-md p-2 styled-block-1">
              <div class="price-block">'.$format_price.' ₽</div>
              <div class="center-text">новая цена</div><br>
              <div class="old-price-block m-2">'.$format_old_price.' ₽</div>
              <div class="center-text">старая цена '.$discount_text.'</div><br>
              <div class="center-text">'.$change_text.'</div><br>
              '.$amount_text.'
              '.$buy_button.'
              <br>
              '.$buy_one_click_button.'
              </div>';
        }else{
            echo '<div class="container-md p-2 styled-block-1">
              <div class="price-block">'.$format_price.' ₽</div>
              <div class="center-text">цена ещё не менялась</div><br>';
        }

    }else{
        echo '<h1>К сожалению, ничего не нашлось ;(</h1>';
    }
    ?>
</div>

<script src="src/js/jquery.min.js"></script>
<script src="src/js/index.js"></script>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<script src="src/js/popper.min.js"></script>
</body>
</html>