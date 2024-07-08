<?php
class AccountsDB
{
    private \mysqli $db;
    public function __construct()
    {
        try{
            $settings = ConfigController::MYSQL_CONNECTION;
            $this->db = new mysqli($settings['addr'], $settings['user'], $settings['password']);
            $this->db->query("CREATE DATABASE IF NOT EXISTS accounts_db;");
            $this->db->query("SET NAMES utf8;");
            $this->db->query("USE accounts_db;");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts (acc_id BIGINT, acc_login TEXT NOT NULL, user_name TEXT, avatar TEXT, password TEXT, devices TEXT, PRIMARY KEY (acc_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts_vk (acc_id BIGINT, vk_id BIGINT, PRIMARY KEY (acc_id, vk_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS accounts_tg (acc_id BIGINT, tg_id BIGINT, PRIMARY KEY (acc_id, tg_id));");
            $this->db->query("CREATE TABLE IF NOT EXISTS 2fa (acc_id BIGINT, 2fa_isEnabled BOOLEAN, 2fa_secret TEXT, PRIMARY KEY (acc_id));");
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
            return $fetched[0]['acc_id'];
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
            return $request->fetch_all(MYSQLI_ASSOC)[0]['devices'];
        }
        return false;
    }
    public function login_get(int $acc_id)
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC)[0]['acc_login'];
        }
        return false;
    }
    public function login_set(int $acc_id, string $new_login): bool
    {
        $get = $this->avatar_get($acc_id);
        if ($get !== false && $new_login !== $get)
        {
            $new_login = mb_strtolower($new_login, 'utf-8');
            if ($result = $this->db->query("UPDATE accounts SET acc_login = '$new_login' WHERE acc_id=$acc_id;") !== false) return true;
        }
        return false;
    }
    public function name_get(int $acc_id)
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC)[0]['user_name'];
        }
        return false;
    }
    public function name_set(int $acc_id, string $new_name): bool
    {
        $get = $this->avatar_get($acc_id);
        if ($get !== false && $new_name !== $get)
        {
            if ($this->db->query("UPDATE accounts SET user_name = '$new_name' WHERE acc_id=$acc_id;") !== false) return true;
        }
        return false;
    }
    public function avatar_get(int $acc_id)
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC)[0]['avatar'];
        }
        return false;
    }
    public function avatar_set(int $acc_id, string $new_avatar): bool
    {
        $get = $this->avatar_get($acc_id);
        if ($get !== false)
        {
            if ($this->db->query("UPDATE accounts SET avatar = '$new_avatar' WHERE acc_id=$acc_id;") !== false) return true;
        }
        return false;
    }

    public function password_get(int $acc_id)
    {
        $request = $this->db->query("SELECT * FROM accounts WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC)[0]['password'];
        }
        return false;
    }
    public function password_set(int $acc_id, string $new_password): bool
    {
        $get = $this->password_get($acc_id);
        if ($get !== false && $new_password !== $get)
        {
            if ($this->db->query("UPDATE accounts SET password = '$new_password' WHERE acc_id=$acc_id;") !== false) return true;
        }
        return false;
    }

    public function two_fa_enabled(int $acc_id)
    {
        $request = $this->db->query("SELECT * FROM 2fa WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            $fetch = $request->fetch_all(MYSQLI_ASSOC);
            if (count($fetch) > 0)
            {
                return $fetch[0]['2fa_isEnabled'];
            }
        }
        return false;
    }
    public function two_fa_delete(int $acc_id): bool
    {
        try {
            if ($this->db->query("DELETE FROM 2fa WHERE `acc_id`=$acc_id;") !== false) return true;
        }catch(mysqli_sql_exception $e)
        {
            (new ErrorManager())->getExceptionLog($e, 'AccountsDB');
        }
        return false;
    }
    public function two_fa_set(int $acc_id, bool $is_enabled, string $secret_code = ''): bool
    {
        try {
            $request = $this->db->query("INSERT IGNORE INTO 2fa (`acc_id`, `2fa_isEnabled`, `2fa_secret`) VALUES ($acc_id, $is_enabled, '$secret_code')");
            if ($request !== false) return true;
        }catch(mysqli_sql_exception $e)
        {
            (new ErrorManager())->getExceptionLog($e, 'AccountsDB');
        }
        return false;
    }
    public function two_fa_get_code(int $acc_id): false|string
    {
        $request = $this->db->query("SELECT * FROM 2fa WHERE acc_id=$acc_id;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC)[0]['2fa_secret'] ?? 'none';
        }
        return false;
    }
    public function two_fa_get_all_secrets(): array|false
    {
        $request = $this->db->query("SELECT 2fa_secret FROM 2fa;");
        if ($request instanceof mysqli_result)
        {
            return $request->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
}