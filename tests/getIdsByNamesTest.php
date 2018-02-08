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

$arr = [
    new Category('Книги ', 10, 0),
    new Category(' Зарубежные', 20, 10),
    new Category('  Русские', 30, 10),
    new Category('Научные', 40, 20),
    new Category('Научные', 50, 30),
    new Category('Художественные', 60, 20),
    new Category('Художественные', 70, 30)
];

$cats = new Categories($arr);

function test(Categories $cats, array $names, $correct_answer)
{
    $answer = $cats->getIdsByNames($names);
    if ($answer == $correct_answer) {
        echo "<p style='color:green'>Тест прошел успешно</p>";
    } else {
        echo "<p style='color:red'>Тест провалился!</p>";
    }
}
test($cats, ['Книги', 'Зарубежные', 'Научные'], [10, 20, 40]);
test($cats, ['Книги', 'Русские', 'Научные'], [10, 30, 50]);
test($cats, ['Книги', 'Русские', 'Новозеландские'], []);
test($cats, [], []);

echo "The End.";
