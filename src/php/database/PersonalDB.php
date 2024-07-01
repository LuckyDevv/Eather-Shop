<?php
class PersonalDB
{
    public \mysqli $db;

    public function __construct()
    {
        try{
            $this->db = new mysqli('localhost', 'root', 'eather1192@How91');
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
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                $newStr = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if ($newStr == '') $newStr = ',';
                $answer = $this->db->execute_query("UPDATE favorites SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function favorites_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                $product_ids = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if (trim($product_ids) == '' or trim($product_ids) == ','){
                    $newStr = $product_id.',';
                }else{
                    $newStr = $product_id.','.$product_ids;
                }
                $answer = $this->db->execute_query("UPDATE favorites SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function favorites_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        return $this->extracted_mysqli($request, $product_id);
    }
    public function cart_products_get_all(int $acc_id): false|array
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        return $this->mysqli_request($request);
    }
    public function cart_delete_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                $newStr = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if ($newStr == '') $newStr = ',';
                $answer = $this->db->execute_query("UPDATE cart SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function cart_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                $product_ids = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if (trim($product_ids) == '' or trim($product_ids) == ','){
                    $newStr = $product_id.',';
                }else{
                    $newStr = $product_id.','.$product_ids;
                }
                $answer = $this->db->query("UPDATE cart SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer !== false) return true;
            }
        }
        return false;
    }
    public function cart_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        return $this->extracted_mysqli($request, $product_id);
    }

    /**
     * @param mysqli_result|bool $request
     * @return false|string[]
     */
    public function mysqli_request(mysqli_result|bool $request): array|false
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
    public function extracted_mysqli(mysqli_result|bool $request, int|string $product_id): bool
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
}