<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/logs/MyLog.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Categories.php';

// Книги-Зарубежные-Научные
// Книги-Зарубежные-Художественные
// Книги-Русские-Научные
// Книги-Русские-Художественные

$arr1 = [
    new Category('Книги ', 10, 0),
    new Category(' Зарубежные', 20, 10),
    new Category('  Русские', 30, 10),
    new Category('Научные', 40, 20),
    new Category('Научные', 50, 30),
    new Category('Художественные', 60, 20),
    new Category('Художественные', 70, 30)
];

$cats1 = new Categories($arr1);
$cat1 = new Category('Сдравствуй опа новый год! ', 100, 70);
$cat2 = new Category('Еще одна категория! ', 100, 10);
$cat3 = new Category('Еще одна категория! ', 110, 100);
$cat4 = new Category('Еще одна категория! ', 110, 100);

function test(Categories $categories, Category $addedCategory, $correct_answer)
{
    try {
        $categories->addCategory($addedCategory);
        if (in_array($addedCategory, $categories->getCategories()) && $correct_answer == 'добавлена') {
            echo "<p style='color:green'>Тест прошел успешно</p>";
        } else {
            echo "<p style='color:red'>Тест провалился!</p>";
        }
    } catch (MyException $ex) {
        if ($correct_answer == 'уже есть') {
            echo "<p style='color:green'>Тест прошел успешно</p>";
        } else {
            echo "<p style='color:red'>Тест провалился!</p>";
        }
    }
}
test($cats1, $cat1, 'добавлена');
test($cats1, $cat2, 'уже есть');
test($cats1, $cat3, 'добавлена');
test($cats1, $cat4, 'уже есть');

echo "The End.";
