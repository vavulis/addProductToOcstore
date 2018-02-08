<?php

class MyLog
{   
    static function log($text, $log_file)
    {
        $fp = fopen($log_file, "a"); // Открываем файл в режиме записи

        if (!$fp) {
            die("Ошибка! Не могу создать файл логов!");
        }

        fwrite($fp, date("Y-m-d H:i:s") . " # " . $_SERVER['REMOTE_ADDR'] . " # $text\r\n");

        fclose($fp); //Закрытие файла
    }
}
