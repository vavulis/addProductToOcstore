<?php
header('Content-Type: text/html; charset=utf-8');

require 'Categories.php';

// Книги-Зарубежные-Научные
// Книги-Зарубежные-Художественные
// Книги-Русские-Научные
// Книги-Русские-Художественные

$arr = [
    new Category('Книги', 10, 0),
    new Category('Зарубежные', 20, 10),
    new Category('Русские', 30, 10),
    new Category('Научные', 40, 20),
    new Category('Научные', 50, 30),
    new Category('Художественные', 60, 20),
    new Category('Художественные', 70, 30)
];

$categories = new Categories($arr);

$categories->generateAllCategoryLists();

$t = ['Книги', 'Русские', 'Духовные', 'Святоотеческие'];
$t2 = ['Книги', 'Русские'];

$otvet = $categories->createOrUpdateCategory($t);

echo "id=$otvet[id]";
$categories->printArray($otvet[categories], 'Категории, которые надо добавить');


