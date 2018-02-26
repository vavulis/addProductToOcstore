<?php
ini_set('max_execution_time', 0);
define('__ROOT__', __DIR__);
header('Content-Type: text/html; charset=utf-8');
require_once __ROOT__ . '/config.php';
require_once __ROOT__ . '/vendor/vavulis/logs/MyLog.php';
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Product.php';

$product = new Product($dbHost, $dbLogin, $dbPassword, $dbName, $oc_version, $language_id);
$product();





