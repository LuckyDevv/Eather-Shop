<?php
class AccountsDB
{
    private \mysqli $db;
    public function __construct()
    {
        try{
            $this->db = new mysqli('localhost', 'root', 'eather1192@How91');
            $this->db->query("CREATE DATABASE IF NOT EXISTS accounts_db;");
            $this->db->query("SET NAMES utf8;");
            $this->db->query("USE accounts_db;");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts (acc_id BIGINT, acc_login TEXT NOT NULL, user_name TEXT, avatar TEXT, password TEXT, devices TEXT, PRIMARY KEY (acc_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts_vk (acc_id BIGINT, vk_id BIGINT, PRIMARY KEY (acc_id, vk_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts_tg (acc_id BIGINT, tg_id BIGINT, PRIMARY KEY (acc_id, tg_id));");
        }catch (mysqli_sql_exception $exception) {
            echo (new ErrorManager())->getExceptionLog($exception, 'AccountsDB');
            die();
        }
    }
    public function account_exists_login(string $login): bool
    {
        $login = strtolower($login);
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_login = '$login';");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
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
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
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
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                return $fetched[0]['acc_id'];
            }
        }
        return false;
    }
    public function account_registration(string $acc_login, string $acc_password, string $ip, string $filePersonalDb): bool
    {
        if (!$this->account_exists_login($acc_login))
        {
            $acc_id = rand(1000000000, 9999999999);
            if (!$this->account_exists_id($acc_id))
            {
                try
                {
                    if ($this->db->query("INSERT INTO accounts VALUES ($acc_id, '$acc_login', 'New User', 'no-img.jpg', '$acc_password', '$ip,');"))
                    {

                        $personal = (new PersonalDB())->db;
                        try {
                            $exec1 = $personal->query("INSERT IGNORE INTO cart VALUES ($acc_id, ',');");
                            $exec2 = $personal->query("INSERT IGNORE INTO favorites VALUES ($acc_id, ',');");
                            $exec3 = $personal->query("INSERT IGNORE INTO wallets VALUES ($acc_id, $acc_id, 0.0, -22);");
                        }catch (mysqli_sql_exception $exception) {
                            echo (new ErrorManager())->getExceptionLog($exception, 'PersonalDB');
                            return false;
                        }
                        $personal->close();
                        unset($personal);
                        if (($exec1 instanceof mysqli_result or $exec1) && ($exec2 instanceof mysqli_result or $exec2) && ($exec3 instanceof mysqli_result or $exec3)) return true;
                    }
                }catch (Exception) {}
            }else return $this->account_registration($acc_login, $acc_password, $ip, $filePersonalDb);
        }
        return false;
    }
    public function account_auth(string $login, string $password, string $ip): bool
    {
        $login = strtolower($login);
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_login = '$login';");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                if ($password == $fetched[0]['password'])
                {
                    if (!$this->devices_exists($fetched[0]['acc_id'], $ip))
                    {
                        $newDevices = $this->devices_get($fetched[0]['acc_id']).$ip.',';
                        $this->db->query("UPDATE accounts SET devices = '$newDevices' WHERE acc_id = ".$fetched[0]['acc_id'].";");
                    }
                    return true;
                }
            }
        }
        return false;
    }
    public function devices_exists(int $acc_id, string $ip): bool
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id = '$acc_id';");
        if ($request instanceof mysqli_result)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                if (in_array($ip, explode(',', $fetched[0]['devices']))) {
                    return true;
                }
            }
        }
        return false;
    }
    public function devices_get(int $acc_id): string|false
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id = '$acc_id';");
        if ($request !== false)
        {
            $fetched = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetched) === 1)
            {
                return $fetched[0]['devices'];
            }
        }
        return false;
    }
}