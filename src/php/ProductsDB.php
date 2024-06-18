<?php
class ProductsDB
{
    private \SQLite3 $db;
    public function __construct(string $fileName)
    {
        $this->db = new \SQLite3($fileName);
        $this->db->exec("CREATE TABLE IF NOT EXISTS sellers (acc_id INTEGER, seller_id INTEGER PRIMARY KEY, seller_name TEXT, seller_description TEXT, seller_photo TEXT, seller_pincode INTEGER);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS products (seller_id INTEGER, product_id INTEGER PRIMARY KEY, product_name TEXT, product_description TEXT, product_photo TEXT, product_price INTEGER, product_amount INTEGER, product_category INTEGER, product_old_price INTEGER, product_sales INTEGER, product_create_date INTEGER, product_update_date INTEGER);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS feedbacks (acc_id INTEGER, product_id INTEGER, stars INTEGER, comment TEXT);");
    }
    public function product_find_by_name(string $required_name): false|array
    {
        $request = $this->db->query("SELECT * FROM products;");
        if($request instanceof SQLite3Result) {
            $fetched = $this->sqliteFetchAll($request);
            if(count($fetched) > 0)
            {
                $result = [];
                foreach ($fetched as $key => $value) {
                    if (str_contains(mb_strtolower($value['product_name'], 'UTF-8'), $required_name)
                        or str_contains(mb_strtolower($value['product_description'], 'UTF-8'), $required_name)) {
                        file_put_contents('src/php/dev_log/log.txt', $required_name.' успешно '.$value['product_name']);
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
        if($request instanceof SQLite3Result)
        {
            $fetched = $this->sqliteFetchAll($request);
            if(count($fetched) === 1)
            {
                return $fetched[0];
            }
        }
        return false;
    }
    public function product_get_top_sales(): false|array{
        $request = $this->db->query("SELECT * FROM products;");
        if($request instanceof SQLite3Result)
        {
            $fetched = $this->sqliteFetchAll($request);
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
        if($request instanceof SQLite3Result)
        {
            $fetched = $this->sqliteFetchAll($request);
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
    function sqliteFetchAll(\SQLite3Result $results, $mode = SQLITE3_ASSOC): array
    {
        $multiArray = [];
        while($result = $results->fetchArray($mode)) {
            $multiArray[] = $result;
        }
        return $multiArray;
    }
}