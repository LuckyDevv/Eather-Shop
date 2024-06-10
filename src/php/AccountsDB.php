<?php
class AccountsDB
{
    private \SQLite3 $db;
    public function __construct(string $fileName)
    {
        $this->db = new \SQLite3($fileName);
        $this->db->exec("CREATE TABLE IF NOT EXISTS accounts (acc_id INTEGER, acc_login TEXT, user_name TEXT, avatar TEXT, password TEXT, devices TEXT, PRIMARY KEY (acc_id, acc_login));");
        $this->db->exec("CREATE TABLE IF NOT EXISTS accounts_vk (acc_id INTEGER, vk_id INTEGER, PRIMARY KEY (acc_id, vk_id));");
        $this->db->exec("CREATE TABLE IF NOT EXISTS accounts_tg (acc_id INTEGER, tg_id INTEGER, PRIMARY KEY (acc_id, tg_id));");
    }
    public function account_exists_login(string $login): bool
    {
        $login = strtolower($login);
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_login = '$login';");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) > 0)
            {
                return true;
            }
        }
        return false;
    }
    public function account_exists_id(int $acc_id): bool
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id = '$acc_id';");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) > 0)
            {
                return true;
            }
        }
        return false;
    }
    public function account_get_by_login(string $acc_login): false|int
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_login='$acc_login';");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                return $fetched[0]['acc_id'];
            }
        }
        return false;
    }
    public function account_registration(string $acc_login, string $acc_password, string $ip): bool
    {
        if (!$this->account_exists_login($acc_login))
        {
            $acc_id = rand(1000000000, 9999999999);
            if (!$this->account_exists_id($acc_id))
            {
                try
                {
                    if ($this->db->exec("INSERT INTO accounts VALUES ($acc_id, '$acc_login', 'New User', 'no-img.jpg', '$acc_password', '$ip,');"))
                    {
                        return true;
                    }
                }catch (Exception) {}
            }else return $this->account_registration($acc_login, $acc_password, $ip);
        }
        return false;
    }
    public function account_auth(string $login, string $password): bool
    {
        $login = strtolower($login);
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_login = '$login';");
        if ($request !== false)
        {
            $fetched = $this->sqliteFetchAll($request);
            if (count($fetched) === 1)
            {
                if ($password == $fetched[0]['password'])
                {
                    return true;
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