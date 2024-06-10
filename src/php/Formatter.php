<?php
class Formatter
{
    public ConfigController $session_config;
    public function __construct(){}

    public function get_header(): string{
        return '<nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <center><h7><a class="nav-link active" aria-current="page" href="index.php"><h3><i class="bi bi-house"></i></h3> Главная</a></h7></center>
                            </li>
                            <li class="nav-item">
                                <center><h7><a id="personal-btn" class="nav-link active" aria-current="page" data-bs-toggle="modal" data-bs-target="#authModal" href="#"><h3><i class="bi bi-person-circle"></i></h3> Личный кабинет</a></h7></center>
                            </li>
                            <li class="nav-item">
                                <center><h7><a class="nav-link active" aria-current="page" href="#"><h3><i class="bi bi-patch-question"></i></h3> Поддержка</a></h7></center>
                            </li>
                        </ul>
                    </div>
                    <form class="d-flex" onsubmit="return false">
                        <input id="search_param" class="form-control me-2 px-3" type="search" placeholder="Поиск" aria-label="Искать на Eather">
                        <button id="search" class="btn btn-outline-success" onclick="vtik()"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                </nav>';
    }
    public function get_cart_button(int $acc_id, int $product_id): string
    {
        $request = (new PersonalDB('src/php/database/Personal_DB.db'))->cart_exists_product($acc_id, $product_id);;
        if ($request) {
            $cart_button = '<button id="delete-cart_'.$product_id.'" class="btn" onclick="deleteCart('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Удалить из корзину">
                        <h4><i class="bi bi-cart-x-fill" style="color: #23d214; -webkit-text-stroke: 1px #000000"></i></h4>
                        </button>';
        }else{
            $cart_button = '<button id="add-cart_'.$product_id.'" class="btn" onclick="addCart('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Добавить в корзину">
                        <h4><i class="bi bi-cart-plus-fill" style="color: #1b6eb9; -webkit-text-stroke: 1px #000000"></i></h4>
                        </button>';
        }
        return $cart_button;
    }

    public function get_heart_button(int $acc_id, int $product_id): string
    {
        $request = (new PersonalDB('src/php/database/Personal_DB.db'))->favorites_exists_product($acc_id, $product_id);;
        if ($request) {
            $heart_button = '<button id="delete_'.$product_id.'" class="btn unheart" onclick="deleteFavorite('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Удалить из избранного">
                         <h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>
                         </button>';
        }else{
            $heart_button = '<button id="add_'.$product_id.'" class="btn heart" onclick="addFavorite('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Добавить в избранное">
                         <h4><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>
                         </button>';
        }
        return $heart_button;
    }

    public function get_link_button(int $product_id): string
    {
        return '<a class="btn" data-bs-toggle="tooltip" title="Карточка товара" href="product.php?product_id='.$product_id.'">
            <h4><i class="bi bi-postcard-fill" style="color: #58d1d2; -webkit-text-stroke: 1px #000000"></i></h4>
            </a>';
    }

    public function get_discount_text(int $price, int $old_price): string
    {
        $discount = round((($old_price - $price) / $old_price) * 100);
        if ($discount >= 1) {
            $format_old_price = number_format($old_price, 2, ',', ' ');
            return '<del style="color: #a0a19f; font-size: 15px">'.$format_old_price.' ₽</del>
                        <t style="color: red; font-size: 16px">-'.$discount.'%</t>';
        }
        return '';
    }

    public function get_cookie_acc_id($cookie): int
    {
        $acc_id = false;
        if (isset($cookie['sess_id']))
        {
            $this->session_config = new ConfigController('src/php/sessions/' . $cookie['sess_id'] . '.json', ConfigController::JSON);
            $login = $this->session_config->exists('login') ? $this->session_config->get('login') : false;
            if ($login !== false)
            {
                $acc_id = (new AccountsDB('src/php/database/AccountsDB.db'))->account_get_by_login($login);
            }
        }
        return (int) $acc_id;
    }

    public function get_cookie_auth($cookie): bool
    {
        $auth = false;
        if (isset($cookie['sess_id']))
        {
            try {
                $this->session_config = new ConfigController('src/php/sessions/' . $cookie['sess_id'] . '.json', ConfigController::JSON);
                if ($this->session_config->exists('login') && $this->session_config->exists('password'))
                {
                    if ((new AccountsDB('src/php/database/AccountsDB.db'))->account_auth($this->session_config->get('login'), $this->session_config->get('password')))
                    {
                        $auth = true;
                    }
                }
            } catch (ErrorException $e) {}
        }
        return $auth;
    }

    public function get_session_config(): ConfigController
    {
        return $this->session_config;
    }

    public function get_products(array $cookie, string $title, array $products, int $limit = 999999, bool $returnIfNull = false, $notLoadMessage = '', bool $isSearch = false): string
    {
        $returnText = '';
        $acc_id = $this->get_cookie_acc_id($cookie);
        if (count($products) >= 1)
        {
            $content = '';
            $count = 0;
            foreach ($products as $key => $product)
            {
                if($count < $limit)
                {
                    if (!$isSearch)
                    {
                        $product_ = (new ProductsDB('src/php/database/Products_DB.db'))->product_find_by_id($product[0]);
                    }else{
                        $product_ = (new ProductsDB('src/php/database/Products_DB.db'))->product_find_by_id($product['product_id']);
                    }
                    if ($product_ !== false)
                    {
                        $price = $product_['product_price'];
                        $format_price = number_format($price, 2, ',', ' ');
                        $photo = 'no-img.jpg';
                        foreach(explode(',', $product_['product_photo']) as $photo_)
                        {
                            if (!is_file('photos/'.$photo_)) continue;
                            $photo = $photo_; break;
                        }
                        $name = $product_['product_name'];
                        if (mb_strlen($name, 'utf-8') >= 57)
                        {
                            $name = mb_substr($name, 0, 57, 'utf-8');
                            $name .= '...';
                        }
                        $content .= '
            <div class="col p-2">
                <div class="card me-2" style="width: 18rem; height: 430px;">
                    <img src="photos/'.$photo.'" class="card-img-top" alt="..." style="height: 300px;">
                    <div class="card-body">
                        <h5 class="card-title"><t style="color: #00bb0e; font-weight: 800">'.$format_price.' ₽</t>
                        '.$this->get_discount_text($price, $product_['product_old_price']).'</h5>
                        <p class="card-text">'.$name.'</p>
                        <div class="card-img-overlay">
                        '.$this->get_heart_button($acc_id, $product_['product_id']).'
                        '.$this->get_cart_button($acc_id, $product_['product_id']).'
                        '.$this->get_link_button($product_['product_id']).'
                        </div>
                    </div>
                </div>
            </div>
            ';
                        $count++;
                    }
                }else break;
            }
            if ($count >= 1)
            {
                $returnText .= '<div class="styled-block-1 p-2"><h3 class="align-content-center">'.$title.'</h3>';
                $returnText .= '<div class="row row-cols-auto row-cols-md-auto p-3 justify-content-center">';
                $returnText .= $content;
                $returnText .= '</div></div>';
                if (count($products) >= $count && $notLoadMessage !== '')
                {
                    echo '<h5 style="center-text">Некоторые товары не удалось загрузить</h5>';
                }
            }else{
                if($returnIfNull)
                {
                    $returnText .= '<div class="styled-block-1 p-2"><h3 class="align-content-center">'.$title.'</h3>';
                    $returnText .= '<br><h4 style="center-text">Пока что тут пусто :(</h4>';
                    $returnText .= '</div></div>';
                }
            }
        }
        return $returnText;
    }

    public function get_personal_products(string $title, int $type): string
    {
        $returnText = '<br>
    <div class="styled-block-1 p-2"><h3 class="align-content-center">' . $title . '</h3>';
        $login = $this->get_session_config()->get('login');
        if ($login !== false)
        {
            $acc_id = (new AccountsDB('src/php/database/AccountsDB.db'))->account_get_by_login($login);
            if ($acc_id !== false)
            {
                $products = $type == 0 ? (new PersonalDB('src/php/database/Personal_DB.db'))->favorites_products_get_all($acc_id) : (new PersonalDB('src/php/database/Personal_DB.db'))->cart_products_get_all($acc_id);
                if ($products !== false)
                {
                    $count_products = 0;
                    $content = '';
                    foreach ($products as $product_id)
                    {
                        if ($product_id == ''){
                            $count_products++;
                            continue;
                        }
                        $product_ = (new ProductsDB('src/php/database/Products_DB.db'))->product_find_by_id($product_id);
                        if ($product_ !== false)
                        {
                            $price = $product_['product_price'];
                            $format_price = number_format($price, 2, ',', ' ');
                            $photo = 'no-img.jpg';
                            foreach (explode(',', $product_['product_photo']) as $photo_) {
                                if (!is_file('photos/' . $photo_)) continue;
                                $photo = $photo_;
                                break;
                            }
                            $name = $product_['product_name'];
                            if (mb_strlen($name, 'utf-8') >= 57)
                            {
                                $name = mb_substr($name, 0, 57, 'utf-8');
                                $name .= '...';
                            }
                            $content .= '
            <div class="col p-2">
                <div class="card me-2" style="width: 18rem; height: 430px;">
                    <img src="photos/' . $photo . '" class="card-img-top" alt="..." style="height: 300px;">
                    <div class="card-body">
                        <h5 class="card-title"><t style="color: #00bb0e; font-weight: 800">' . $format_price . ' ₽</t>
                        ' . $this->get_discount_text($price, $product_['product_old_price']) . '</h5>
                        <p class="card-text">'.$name.'</p>
                        <div class="card-img-overlay">
                        ' . $this->get_heart_button($acc_id, $product_['product_id']) . '
                        ' . $this->get_cart_button((int) $acc_id, $product_['product_id']) . '
                        ' . $this->get_link_button($product_['product_id']) . '
                        </div>
                    </div>
                </div>
            </div>
            ';
                            $count_products++;
                        }else continue;
                    }
                    if ($count_products >= 1) {
                        $returnText .= '<div class="row row-cols-auto row-cols-md-auto p-3 justify-content-center">';
                        $returnText .= $content;
                        $returnText .= '</div>';
                        if (count($products) !== $count_products)
                        {
                            $returnText .= '<h5 style="center-text">Некоторые товары не удалось загрузить</h5>';
                        }
                    }else{
                        $returnText .= '<br><h4 style="center-text">Пока что тут пусто :(</h4>';
                    }
                }else{
                    $returnText .= '<br><h4 style="center-text">Пока что тут пусто :(</h4>';
                }
            }
        }
        $returnText .= '</div>';
        return $returnText;
    }
}