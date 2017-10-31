<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'MyException.php';

// Подключаем апи ocStore
require 'Product.php';

$product = new Product();
$product();





