<?php
// Подключаемся к базе данных
require_once 'config.php';
require_once 'rb-mysql.php';
R::setup( 'mysql:host=$dbHost;dbname=$dbName', '$dbLogin', '$dbPassword' );

// Подключаем Api работы с товарами в OcStore
require_once 'addProduct.php';

// Подключается к базе данных


// Цикл, в котором мы добавляем все товары


// Отключается от базы данных
?>