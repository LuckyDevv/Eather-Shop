<?php
class Functions
{
    public ConfigController $session_config;
    public function __construct(){}

    public function get_header(bool $isMobile = false): string{
        if ($isMobile) {
            return $this->getTemplate('header_mobile');
        }else{
            return $this->getTemplate('header');
        }
    }
    public function get_header_script(): string {
        return "<script>
    const items = document.querySelectorAll('.nav-item');
    let maxWidth = 0;

    items.forEach((item) => {
        if (item.clientWidth > maxWidth) {
            maxWidth = item.clientWidth;
            item.style.width = maxWidth + 'px';
        }
    });

    items.forEach((item) => {
        item.style.width = maxWidth + 'px';
    });
</script>";
    }
    public function get_cart_button(int $acc_id, int $product_id): string
    {
        $request = (new PersonalDB())->cart_exists_product($acc_id, $product_id);
        if ($request) {
            $cart_button = '<button id="delete-cart_'.$product_id.'" class="btn" onclick="deleteCart('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Удалить из корзину">
                        <h4 class="cart-button-size"><i class="bi bi-cart-x-fill" style="color: #23d214; -webkit-text-stroke: 1px #000000"></i></h4>
                        </button>';
        }else{
            $cart_button = '<button id="add-cart_'.$product_id.'" class="btn" onclick="addCart('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Добавить в корзину">
                        <h4 class="cart-button-size"><i class="bi bi-cart-plus-fill" style="color: #1b6eb9; -webkit-text-stroke: 1px #000000"></i></h4>
                        </button>';
        }
        return $cart_button;
    }

    public function get_heart_button(int $acc_id, int $product_id): string
    {
        $request = (new PersonalDB())->favorites_exists_product($acc_id, $product_id);
        if ($request) {
            $heart_button = '<button id="delete_'.$product_id.'" class="btn unheart" onclick="deleteFavorite('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Удалить из избранного">
                         <h4 class="favorite-button-size"><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>
                         </button>';
        }else{
            $heart_button = '<button id="add_'.$product_id.'" class="btn heart" onclick="addFavorite('.$acc_id.', '.$product_id.', this)" data-bs-toggle="tooltip" title="Добавить в избранное">
                         <h4 class="favorite-button-size"><i class="bi bi-heart-fill" style="-webkit-text-stroke: 2px #000000;"></i></h4>
                         </button>';
        }
        return $heart_button;
    }

    public function get_discount_text(int $price, int $old_price): string
    {
        if ($old_price == 0) return '';
        $discount = round((($old_price - $price) / $old_price) * 100);
        if ($discount >= 1) {
            $format_old_price = number_format($old_price, 2, ',', ' ');
            return '<del style="color: #a0a19f; font-size: 15px">'.$format_old_price.' ₽</del>
                        <t style="color: red; font-size: 16px">-'.$discount.'%</t>';
        }
        return '';
    }

    public function get_session_acc_id(string $session, bool $isHandler = false): int
    {
        if ($isHandler) {
            $path_sessions = '../sessions/' . $session . '.json';
        }else{
            $path_sessions = 'sessions/' . $session . '.json';
        }
        $acc_id = false;
        if (file_exists($path_sessions))
        {
            $this->session_config = new ConfigController($path_sessions, ConfigController::JSON);
            $login = $this->session_config->exists('login') ? $this->session_config->get('login') : false;
            if ($login !== false)
            {
                $acc_id = (new AccountsDB())->account_get_by_login($login);
            }else{
                unlink($path_sessions);
            }
        }
        return (int) $acc_id;
    }

    public function get_cookie_acc_id($cookie): int
    {
        $acc_id = false;
        if (isset($cookie['sess_id']))
        {
            if (file_exists('src/php/sessions/' . $cookie['sess_id'] . '.json'))
            {
                $this->session_config = new ConfigController('src/php/sessions/' . $cookie['sess_id'] . '.json', ConfigController::JSON);
                $login = $this->session_config->exists('login') ? $this->session_config->get('login') : false;
                if ($login !== false)
                {
                    $acc_id = (new AccountsDB())->account_get_by_login($login);
                }else{
                    unlink('src/php/sessions/' . $cookie['sess_id'] . '.json');
                }
            }
        }
        return (int) $acc_id;
    }

    public function get_cookie_auth($cookie, $ip): bool
    {
        $auth = false;
        if (isset($cookie['sess_id']))
        {
            try {
                if (file_exists('src/php/sessions/' . $cookie['sess_id'] . '.json'))
                {
                    $this->session_config = new ConfigController('src/php/sessions/' . $cookie['sess_id'] . '.json', ConfigController::JSON);
                    if ($this->session_config->exists('login') && $this->session_config->exists('password'))
                    {
                        if ((new AccountsDB())->account_auth($this->session_config->get('login'), $this->session_config->get('password'), $ip))
                        {
                            $auth = true;
                        }else{
                            unlink('src/php/sessions/' . $cookie['sess_id'] . '.json');
                        }
                    }else{
                        unlink('src/php/sessions/' . $cookie['sess_id'] . '.json');
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
                    $product_ = !$isSearch ?
                        (new ProductsDB())->product_find_by_id($product[0]) :
                        (new ProductsDB())->product_find_by_id($product['product_id']);
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
                        if (mb_strlen($name, 'utf-8') >= 20)
                        {
                            $name = mb_substr($name, 0, 20, 'utf-8');
                            $name .= '...';
                        }
                        $content .= str_replace(array(
                            '%product_id%',
                            '%photo%',
                            '%price%',
                            '%name%',
                            '%heart_button%',
                            '%cart_button%'),
                            array(
                                $product_['product_id'],
                                'photos/'.$photo,
                                '<t style="color: #00bb0e; font-weight: 800">'.$format_price.' ₽</t><br>'.$this->get_discount_text($price, $product_['product_old_price']),
                                $name,
                                $this->get_heart_button($acc_id, $product_['product_id']),
                                $this->get_cart_button($acc_id, $product_['product_id'])
                            ),
                            $this->getTemplate('products'));
                        $count++;
                    }
                }else break;
            }
            if ($count >= 1)
            {
                $returnText .= '<div class="styled-block-1 p-2"><h3 class="align-content-center">'.$title.'</h3>
                                    <div class="row row-cols-auto row-cols-md-auto p-3 justify-content-center">'
                    .$content.
                    '</div>
                                </div>';
                if (count($products) >= $count && $notLoadMessage !== '') $returnText .= '<h5 style="center-text">Некоторые товары не удалось загрузить</h5>';
            }else $returnText .= $returnIfNull ? '<div class="row row-cols-auto row-cols-md-auto p-3 justify-content-center">
                                                      <br><h4 style="center-text">Пока что тут пусто :(</h4>
                                                  </div>' : '';
        }
        $returnText .= '</div>';
        return $returnText;
    }

    public function get_personal_products(int $type): string
    {
        $class = $type == 0 ? 'favorite' : 'cart';
        $title = $type == 0 ? 'ИЗБРАННОЕ' : 'КОРЗИНА';
        $returnText = '<div class="block-card block-'.$class.'">
        <div class="block-title title-'.$class.'">
            <p>'.$title.'</p>
        </div>
        <div class="img-block align-content-center">';
        $image_tpl = '<img src="photos/%photo1%" class="img-block img1 img-shadow" alt="">
                    <img src="photos/%photo2%" class="img-block img2 img-shadow" alt="">
                    <img src="photos/%photo3%" class="img-block img3 img-shadow" alt="">';
        $login = $this->get_session_config()->get('login');
        if ($login !== false)
        {
            $acc_id = (new AccountsDB())->account_get_by_login($login);
            if ($acc_id !== false)
            {
                $products = $type == 0 ? (new PersonalDB())->favorites_products_get_all($acc_id) : (new PersonalDB('src/php/database/database/Personal_DB.db'))->cart_products_get_all($acc_id);
                if ($products !== false) {
                    $count_products = 1;
                    foreach ($products as $product_id) {
                        if ($product_id == '') {
                            $count_products++;
                            continue;
                        }
                        if ($count_products >= 4) break;
                        $product_ = (new ProductsDB())->product_find_by_id($product_id);
                        if ($product_ !== false) {
                            $photo = 'no-img.jpg';
                            foreach (explode(',', $product_['product_photo']) as $photo_) {
                                if (!is_file('photos/' . $photo_)) continue;
                                $photo = $photo_;
                                break;
                            }
                            $image_tpl = str_replace(array(
                                '%product_id'.$count_products.'%',
                                '%photo'.$count_products.'%'),
                                array(
                                    $product_['product_id'],
                                    $photo,
                                ),
                                $image_tpl);
                            $count_products++;
                        } else continue;
                    }
                    if ($count_products >= 1) {
                        $returnText .= $image_tpl;
                    } else $returnText .= '<h2>Тут пусто :(</h2>';
                } else $returnText .= '<h2>Тут пусто :(</h2>';
            }
        }
        $returnText .= '</div></div>';
        return $returnText;
    }
    public function getTemplate(string $name): false|string
    {
        ob_start();
        include('tpl/'.$name.".tpl");
        return ob_get_clean();
    }
}