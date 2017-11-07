<?php

header('Content-Type: text/html; charset=utf-8');

require 'Category.php';

$arr = [
    new Category('Книги', 1, 0),
    new Category('Зарубежные', 2, 1),
    new Category('Русские', 3, 1),
    new Category('Научные', 4, 2),
    new Category('Научные', 5, 3),
    new Category('Художественные', 6, 2),
    new Category('Художественные', 7, 3)
];

var_dump($arr);

