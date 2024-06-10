<?php
class PersonalDB
{
    private \SQLite3 $db;

    public function __construct(string $fileName)
    {
        $this->db = new \SQLite3($fileName);
        $this->db->exec("CREATE TABLE IF NOT EXISTS cart (acc_id INTEGER PRIMARY KEY, products_id TEXT);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS orders (acc_id INTEGER, order_id INTEGER, products_id TEXT, status INTEGER, payed REAL, PRIMARY KEY (acc_id, order_id));");
        $this->db->exec("CREATE TABLE IF NOT EXISTS wallets (acc_id INTEGER, wallet_id INTEGER, wallet_balance REAL, wallet_pincode INTEGER, PRIMARY KEY (acc_id, wallet_id));");
        $this->db->exec("CREATE TABLE IF NOT EXISTS favorites (acc_id INTEGER PRIMARY KEY, products_id STRING);");
    }
    public function favorites_products_get_all(int $acc_id): false|array
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $product_ids = $fetched[0]['products_id'];
                if (trim($product_ids) !== '' && trim($product_ids) !== ','){
                    return explode(',', $product_ids);
                }
            }
        }
        return false;
    }
    public function favorites_delete_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $newStr = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if ($newStr == '') $newStr = ',';
                $answer = $this->db->exec("UPDATE favorites SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function favorites_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $product_ids = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if (trim($product_ids) == '' or trim($product_ids) == ','){
                    $newStr = $product_id.',';
                }else{
                    $newStr = $product_id.','.$product_ids;
                }
                $answer = $this->db->exec("UPDATE favorites SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function favorites_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM favorites WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                if (is_numeric($product_id))
                {
                    $product_id = (int) $product_id;
                    foreach (explode(',', $fetched[0]['products_id']) as $product)
                    {
                        if ($product == '') continue;
                        if ($product_id == (int) $product) return true;
                    }
                }
            }
        }
        return false;
    }
    public function cart_products_get_all(int $acc_id): false|array
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $product_ids = $fetched[0]['products_id'];
                if (trim($product_ids) !== '' && trim($product_ids) !== ','){
                    return explode(',', $product_ids);
                }
            }
        }
        return false;
    }
    public function cart_delete_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $newStr = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if ($newStr == '') $newStr = ',';
                $answer = $this->db->exec("UPDATE cart SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function cart_add_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                $product_ids = str_replace("{$product_id},", '', $fetched[0]['products_id']);
                if (trim($product_ids) == '' or trim($product_ids) == ','){
                    $newStr = $product_id.',';
                }else{
                    $newStr = $product_id.','.$product_ids;
                }
                $answer = $this->db->exec("UPDATE cart SET products_id='$newStr' WHERE acc_id=$acc_id;");
                if ($answer) return true;
            }
        }
        return false;
    }
    public function cart_exists_product(int $acc_id, int|string $product_id): bool
    {
        $request = $this->db->query("SELECT * FROM cart WHERE acc_id=$acc_id;");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                if (is_numeric($product_id))
                {
                    $product_id = (int) $product_id;
                    foreach (explode(',', $fetched[0]['products_id']) as $product)
                    {
                        if ($product == '') continue;
                        if ($product_id == (int) $product) return true;
                    }
                }
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