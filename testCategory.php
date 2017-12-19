<?php
define('__ROOT__', __DIR__);
header('Content-Type: text/html; charset=utf-8');

require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/logs/MyLog.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Categories.php';

$tt = [
    new Category('книги', 1, 0),
    new Category('зарубежные', 2, 1),
    new Category('художественные', 3, 2),
    new Category('научные', 4, 3),
    new Category('русские', 5, 1),
    new Category('художественные', 6, 5),
    new Category('научные', 7, 5)
];



$cats = new Categories($tt);

$x = $cats->getParentsChain(7);

var_dump($x);








