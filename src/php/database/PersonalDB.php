<?php
class PersonalDB
{
    public \mysqli $db;

    public function __construct()
    {
        try{
            $settings = ConfigController::MYSQL_CONNECTION;
            $this->db = new mysqli($settings['addr'], $settings['user'], $settings['password']);
            $this->db->query("CREATE DATABASE IF NOT EXISTS personal_db;");
            $this->db->query("SET NAMES utf8;");
            $this->db->query("USE personal_db;");
            $this->db->query("CREATE TABLE IF NOT EXISTS cart (acc_id BIGINT PRIMARY KEY, products_id TEXT);");
            $this->db->query("CREATE TABLE IF NOT EXISTS orders (acc_id BIGINT, order_id INTEGER, products_id TEXT, status INTEGER, payed REAL, PRIMARY KEY (acc_id, order_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS wallets (acc_id BIGINT, wallet_id BIGINT, wallet_balance REAL, wallet_pincode INTEGER, PRIMARY KEY (acc_id, wallet_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS favorites (acc_id BIGINT PRIMARY KEY, products_id TEXT);");
        }catch (mysqli_sql_exception $exception) {
            echo (new ErrorManager())->getExceptionLog($exception, 'PersonalDB');
            die();
        }
    }
    public function favorites_products_get_all(int $acc_id): false|array
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        return $this->mysqli_request($request);
    }
    public function favorites_delete_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        $newStr = $this->get_delete_products_id($request, $product_id);
        $answer = $this->db->query("INSERT INTO favorites (acc_id, products_id) VALUES ($acc_id, $newStr) ON DUPLICATE KEY UPDATE products_id=$newStr;");
        return ($answer instanceof mysqli_result) ? true : $answer;
    }
    public function favorites_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        $newStr = $this->get_add_products_id($request, $product_id);
        $answer = $this->db->query("INSERT INTO favorites (acc_id, products_id) VALUES ($acc_id, $newStr) ON DUPLICATE KEY UPDATE products_id=$newStr;");
        return ($answer instanceof mysqli_result) ? true : $answer;
    }
    public function favorites_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        return $this->exists($request, $product_id);
    }
    public function cart_products_get_all(int $acc_id): false|array
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        return $this->mysqli_request($request);
    }
    public function cart_delete_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        $newStr = $this->get_delete_products_id($request, $product_id);
        $answer = $this->db->query("INSERT INTO cart (acc_id, products_id) VALUES ($acc_id, $newStr) ON DUPLICATE KEY UPDATE products_id=$newStr;");
        return ($answer instanceof mysqli_result) ? true : $answer;
    }
    public function cart_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        $newStr = $this->get_add_products_id($request, $product_id);
        $answer = $this->db->query("INSERT INTO cart (acc_id, products_id) VALUES ($acc_id, $newStr) ON DUPLICATE KEY UPDATE products_id=$newStr;");
        return ($answer instanceof mysqli_result) ? true : $answer;
    }
    public function cart_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        return $this->exists($request, $product_id);
    }

    /**
     * @param mysqli_result|bool $request
     * @return false|string[]
     */
    private function mysqli_request(mysqli_result|bool $request): array|false
    {
        if ($request instanceof mysqli_result) {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1) {
                $product_ids = $fetched[0]['products_id'];
                if (trim($product_ids) !== '' && trim($product_ids) !== ',') {
                    return explode(',', $product_ids);
                }
            }
        }
        return false;
    }

    /**
     * @param mysqli_result|bool $request
     * @param int|string $product_id
     * @return bool
     */
    private function exists(mysqli_result|bool $request, int|string $product_id): bool
    {
        if ($request instanceof mysqli_result) {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1) {
                if (is_numeric($product_id)) {
                    $product_id = (int)$product_id;
                    foreach (explode(',', $fetched[0]['products_id']) as $product) {
                        if ($product == '') continue;
                        if ($product_id == (int)$product) return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param mysqli_result|bool $request
     * @param int|string $product_id
     * @return string
     */
    private function get_add_products_id(mysqli_result|bool $request, int|string $product_id): string
    {
        $product_ids = ',';
        if ($request instanceof mysqli_result) {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1) {
                $product_ids = str_replace("{$product_id},", '', $fetched[0]['products_id']);
            }
        }
        if (trim($product_ids) == '' or trim($product_ids) == ',') {
            $newStr = $product_id . ',';
        } else {
            $newStr = $product_id . ',' . $product_ids;
        }
        return $newStr;
    }

    /**
     * @param mysqli_result|bool $request
     * @param int|string $product_id
     * @return array|string|string[]
     */
    private function get_delete_products_id(mysqli_result|bool $request, int|string $product_id): string|array
    {
        $newStr = '';
        if ($request instanceof mysqli_result) {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1) {
                $newStr = str_replace("{$product_id},", '', $fetched[0]['products_id']);
            }
        }
        if ($newStr == '') $newStr = ',';
        return $newStr;
    }
}