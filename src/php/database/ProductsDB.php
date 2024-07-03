<?php
class ProductsDB
{
    private \mysqli $db;
    public function __construct()
    {
        try {
            $settings = ConfigController::MYSQL_CONNECTION;
            $this->db = new mysqli($settings['addr'], $settings['user'], $settings['password']);
            $this->db->query("CREATE DATABASE IF NOT EXISTS products_db;");
            $this->db->query("SET NAMES utf8;");
            $this->db->query("USE products_db;");
            $this->db->query("CREATE TABLE IF NOT EXISTS sellers (acc_id BIGINT, seller_id BIGINT PRIMARY KEY, seller_name TEXT, seller_description TEXT, seller_photo TEXT, seller_pincode INTEGER);");
            $this->db->query("CREATE TABLE IF NOT EXISTS products (seller_id BIGINT, product_id BIGINT PRIMARY KEY, product_name TEXT, product_description TEXT, product_photo TEXT, product_price INTEGER, product_amount INTEGER, product_category INTEGER, product_old_price INTEGER, product_sales INTEGER, product_create_date INTEGER);");
            $this->db->query("CREATE TABLE IF NOT EXISTS feedbacks (acc_id BIGINT, product_id BIGINT, stars INTEGER, comment TEXT);");
        }catch (mysqli_sql_exception $exception) {
            echo (new ErrorManager())->getExceptionLog($exception, 'ProductsDB');
            die();
        }

    }
    public function product_find_by_name(string $required_name): false|array
    {
        $request = $this->db->query("SELECT * FROM products;");
        if($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if(count($fetched) > 0)
            {
                $result = [];
                foreach ($fetched as $key => $value) {
                    if (str_contains(mb_strtolower($value['product_name'], 'UTF-8'), $required_name)
                        or str_contains(mb_strtolower($value['product_description'], 'UTF-8'), $required_name)) {
                        if(!isset($result[$value['product_id']]))
                        {
                            $result[$value['product_id']] = $value['product_id'];
                        }
                    }else{
                        unset($fetched[$key]);
                    }
                }
                if(count($fetched) >= 1) return $fetched;
            }
        }
        return false;
    }
    public function product_find_by_id(int $required_id): false|array
    {
        $request = $this->db->query("SELECT * FROM products WHERE product_id=$required_id;");
        if($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if(count($fetched) === 1)
            {
                return $fetched[0];
            }
        }
        return false;
    }
    public function product_get_top_sales(): false|array{
        $request = $this->db->query("SELECT * FROM products;");
        if($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if(count($fetched) >= 1)
            {
                $products = [];
                foreach ($fetched as $value)
                {
                    $products[$value['product_id']] = $value['product_sales'];
                }
                arsort($products);
                $top = [];
                foreach($products as $product_id => $sales){
                    $top[] = [$product_id, $sales];
                }
                return $top;
            }
        }
        return false;
    }
    public function product_get_news(): false|array
    {
        $request = $this->db->query("SELECT * FROM products;");
        if($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if(count($fetched) >= 1)
            {
                $products = [];
                foreach ($fetched as $value)
                {
                    $products[$value['product_id']] = $value['product_create_date'];
                }
                arsort($products);
                $top = [];
                foreach($products as $product_id => $sales){
                    $top[] = [$product_id, $sales];
                }
                return $top;
            }
        }
        return false;
    }
}