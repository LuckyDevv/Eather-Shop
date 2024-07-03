<?php
require 'src/php/database/ProductsDB.php';
require 'src/php/database/PersonalDB.php';
require 'src/php/ConfigController.php';
require 'src/php/database/AccountsDB.php';
require 'src/php/Functions.php';
require 'src/php/ErrorManager.php';
$formatter = new Functions();
$products_db = new ProductsDB();
$product_id = $_GET['product_id'] ?? null;
$find = false;
$acc_id = $formatter->get_cookie_acc_id($_COOKIE, $_SERVER['REMOTE_ADDR']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="src/css/bootstrap.min.css" rel="stylesheet">
    <link href="src/icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="src/css/product.css" rel="stylesheet">
    <link href="src/css/gallery.css" rel="stylesheet">
    <link href="src/toastr/toastr.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo/logo.png">
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
<div class="container-fluid margin-custom">
    <div class="row p-1 justify-content-center">
    <?php
    if($find)
    {
        $image = '';

        $price = $product['product_price'];
        $old_price = $product['product_old_price'];
        $photo = $product['product_photo'];

        $photos = explode(',', $photo);
        if (count($photos) >= 1) {
            foreach($photos as $photo) {
                if (!is_file('photos/'.$photo)) continue;
                $image .= '<img src="photos/'.$photo.'" class="C-slide img-thumbnail img-my-drop" alt="">';
            }
        }else{
            $image = '<img src="photos/no-img.jpg" class="C-slide img-thumbnail gallery-image" alt="">';
        }

        $feedbacks = 0;
        $feedback_rate = ($feedbacks > 0 ? 5 / $feedbacks : 0);
        if ($feedback_rate >= 4) {
            $star_classes = 'bi bi-star-fill star-class';
            $star_text_classes = 'star-class';
        }elseif($feedback_rate >= 3 && $feedback_rate <= 3.9){
            $star_classes = 'bi bi-star-half star-class';
            $star_text_classes = 'star-class';
        }else{
            $star_classes = 'bi bi-star-fill star-class-bad';
            $star_text_classes = '';
        }

        $format_price = number_format($price, 2, ',', ' ');
        $amount = $product['product_amount'];

        if ($amount > 0) {
            $amount_text = '<div class="center-text text-delivery"></div>';
            if ($acc_id)
            {
                if (!(new PersonalDB())->cart_exists_product($acc_id, $product_id))
                {
                    $buy_button = '<button type="button" onclick="add_to_cart('.$acc_id.', '.$product_id.', this)" style="background: #0d6efd" class="btn btn-primary buy-button">Добавить в корзину</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
                }else{
                    $buy_button = '<button type="button" onclick="del_from_cart('.$acc_id.', '.$product_id.', this)" style="background: #00bb0e; border: 1px solid #00bb0e;" class="btn btn-primary buy-button">Удалить из корзины</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
                }
            }else{
                $buy_button = '<button type="button" id="personal-btn" aria-current="page" data-bs-toggle="modal" data-bs-target="#authModal" class="btn btn-primary buy-button">Добавить в корзину</button>
                           <div class="text-delivery"> В наличии <b>'.$amount.' штук</b>. Доставим <b>завтра</b></div>';
            }
            $buy_one_click_button = '<button type="button" class="btn btn-info buy-one-click-button">Купить в один клик</button>';
        }else{
            $amount_text = '';
            $buy_button = '<button type="button" class="btn btn-primary buy-button disabled">Товар закончился</button>';
            $buy_one_click_button = '';
        }

        if($old_price !== 0){
            try {
                $discount = round((($old_price - $price) / $old_price) * 100);

                $discount_text = $discount >= 1 ? '<t style="color: red; font-weight: bolder">-'.$discount.'%</t>' : null;
                $format_old_price = number_format($old_price, 2, ',', ' ');
                if($discount < 0){
                    $change_text = '<i class="bi bi-graph-up-arrow" style="color: red"></i> Цена повысилась';
                }else{
                    $change_text = '<i class="bi bi-graph-down-arrow" style="color: #00bb0e;"></i> Цена понизилась';
                }
                $change_text = '<div class="center-text">новая цена</div><br>
                            <div class="old-price-block m-2">'.$format_old_price.' ₽</div>
                            <div class="center-text">старая цена '.$discount_text.'</div><br>
                            <div class="center-text">'.$change_text.'</div><br>';
            }catch(\DivisionByZeroError $e){
                (new ErrorManager())->getExceptionLog($e, 'product');
                $change_text = '<div class="center-text">не удалось получить данные об изменениях цены</div><br>';
            }
        }else{
            $change_text = '<div class="center-text">цена ещё не менялась</div><br>';
        }

        echo str_replace(array(
                '%image%',
                '%product_name%',
                '%star_classes%',
                '%star_text_classes%',
                '%feedback_rate%',
                '%feedbacks%',
                '%product_sales%',
                '%product_description%',
                '%format_price%',
                '%change_price%',
                '%amount_text%',
                '%buy_button%',
                '%buy_one_click_button%'
        ), array(
              $image,
              $product['product_name'],
              $star_classes,
              $star_text_classes,
              $feedback_rate,
              $feedbacks,
              $product['product_sales'],
              $product['product_description'],
              $format_price,
              $change_text,
              $amount_text,
              $buy_button,
              $buy_one_click_button
        ),
        $formatter->getTemplate('product'));
    }else{
        echo '<h1>К сожалению, ничего не нашлось ;(</h1>';
    }
    ?>
    </div>
</div>

<script src="src/js/jquery.min.js"></script>
<script src="src/toastr/toastr.js"></script>
<script src="src/js/product.js"></script>
<script src="src/js/main.js"></script>
<script src="src/js/gallery.js"></script>
<script src="src/js/bootstrap.min.js"></script>
<?php echo $formatter->get_header_script(); ?>
</body>
</html>