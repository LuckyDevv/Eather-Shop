<?php
require '../ConfigController.php';
require '../database/AccountsDB.php';
while (true) {
    foreach (scandir('../sessions') as $session) {
        if ($session !== '.' && $session !== '..') {
            try {
                $session_config = new ConfigController('../sessions/' . $session, ConfigController::JSON);
            }catch (Exception $e) {
                unlink('sessions/' . $session);
                echo "[LOG] The corrupted session file has been deleted.\nFile name: ".$session."\n\n";
                continue;
            }
            $timeout = $session_config->get('timeout');
            $login = $session_config->get('login');
            unset($session_config);
            if (time() >= (int) $timeout) {
                unlink('sessions/' . $session);
                echo "[LOG] The outdated session file has been deleted: ".$login."\nFile name: ".$session."\n\n";
            }
            unset($timeout);
            unset($login);
        }
    }

    $temp_dir = scandir('temp');
    foreach ($temp_dir as $secret) {
        if ($secret == '.' || $secret == '..') {
            $index = array_search($secret, $temp_dir);
            if ($index !== false && isset($temp_dir[$index]))
            {
                unset($temp_dir[$index]);
            }
        }
    }
    if (count($temp_dir) > 0)
    {
        $secrets = (new AccountsDB())->two_fa_get_all_secrets();
        if ($secrets !== false)
        {
            foreach ($temp_dir as $secret)
            {
                if (!in_array($secret, $secrets))
                {
                    unlink('temp/'.$secret);
                }
            }
        }
        unset($secrets);
    }
    unset($temp_dir);

    sleep(3600);
}