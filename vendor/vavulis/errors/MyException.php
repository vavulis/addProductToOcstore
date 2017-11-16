<?php
//namespace vavulis\errors;
//error_reporting(E_ALL);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(E_ERROR);
ini_set('display_errors', 1);

class MyException extends Exception
{

    var $log_file = __ROOT__ . '/logs/error.log';

    function toLog($text)
    {
        // пишем лог в файл $this->errors_log
        $fp = fopen($this->log_file, "a"); // Открываем файл в режиме записи
        $test = fwrite($fp, date("Y-m-d H:i:s") . " # $text\r\n");
        if (!$test) {
            die("Ошибка! Не могу создать файл логов!");
        }
        fclose($fp); //Закрытие файла
    }

    function __construct($message)
    {
        parent::__construct($message);
        $this->toLog($this->getMessage());
    }
}
