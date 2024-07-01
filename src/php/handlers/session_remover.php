<?php
require '../ConfigController.php';
while (true) {
    foreach (scandir('sessions') as $session) {
        if ($session !== '.' && $session !== '..') {
            try {
                $session_config = new ConfigController('sessions/' . $session, ConfigController::JSON);
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
        }
    }
    sleep(3600);
}