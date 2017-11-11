<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Categories.php';

// Книги-Зарубежные-Научные
// Книги-Зарубежные-Художественные
// Книги-Русские-Научные
// Книги-Русские-Художественные

$arr = [
    new \vavulis\parserOpencart\Category('Книги ', 10, 0),
    new \vavulis\parserOpencart\Category(' Зарубежные', 20, 10),
    new \vavulis\parserOpencart\Category('  Русские', 30, 10),
    new \vavulis\parserOpencart\Category('Научные', 40, 20),
    new \vavulis\parserOpencart\Category('Научные', 50, 30),
    new \vavulis\parserOpencart\Category('Художественные', 60, 20),
    new \vavulis\parserOpencart\Category('Художественные', 70, 30)
];

$arr2 = [];

$t0 = [''];
$t1 = ['Книги  ', ' Русские', 'Духовные', 'Святоотеческие'];
$t2 = ['Книги', 'Русские', 'Духовные', 'Жития святых'];
$t3 = ['Книги', 'Греческие', 'Духовные', 'Святоотеческие'];
$t4 = ['Старинные издания ', 'Русские', 'Духовные', 'Святоотеческие'];
$t5 = ['Книги', '  Зарубежные', 'Научные'];
$t6 = ['Книги', 'Зарубежные', 'Научные', 'Англоязычные'];

function test($oc_categories, $new_category, $correct_answer)
{
    $categories = new \vavulis\parserOpencart\Categories($oc_categories);
    $answer = $categories->createOrUpdateCategory($new_category);
    if ($answer == $correct_answer) {
        echo "<p style='color:green'>Тест прошел успешно</p>";
    } else {
        echo "<p style='color:red'>Тест провалился!</p>";
    }
}
test($arr, $t0, NULL);


test($arr, $t1, [
    'id' => 30,
    'categories' => ['Духовные', 'Святоотеческие']
]);

test($arr, $t2, [
    'id' => 30,
    'categories' => ['Духовные', 'Жития святых']
]);

test($arr, $t3, [
    'id' => 10,
    'categories' => ['Греческие', 'Духовные', 'Святоотеческие']
]);

test($arr, $t4, [
    'id' => 0,
    'categories' => ['Старинные издания', 'Русские', 'Духовные', 'Святоотеческие']
]);

test($arr2, $t1, [
    'id' => 0,
    'categories' => ['Книги', 'Русские', 'Духовные', 'Святоотеческие']
]);

test($arr, $t5, NULL);

test($arr, $t6, [
    'id' => 40,
    'categories' => ['Англоязычные']
]);

test($arr2, $t0, NULL);



