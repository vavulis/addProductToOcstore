<?php

use vavulis\MyTest;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Price.php';
require_once __ROOT__ . '/vendor/vavulis/test/MyTest.php';

////////////////////////////////////////////////////////////////////////////////
// ТЕСТЫ. test($price, $markup, $correct_answer)
////////////////////////////////////////////////////////////////////////////////
$tests = [
    [
        '123',
        100,
        [
            'status' => 'ok',
            'correct_price' => '246.0000'
        ]
    ],
    [
        '',
        100,
        [
            'status' => 'error',
            'msg' => 'Не задана цена!'
        ]
    ],
    [
        ' 123 500',
        100,
        [
            'status' => 'ok',
            'correct_price' => '247000.000'
        ]
    ],
    [
        '12;$a3',
        100,
        [
            'status' => 'error',
            'msg' => 'В цене есть недопустимые символы!'
        ]
    ],
    [
        '123.500 ',
        100,
        [
            'status' => 'error',
            'msg' => 'В цене есть недопустимые символы!'
        ]
    ],
    [
        '123,500 ',
        100,
        [
            'status' => 'error',
            'msg' => 'В цене есть недопустимые символы!'
        ]
    ]
];

////////////////////////////////////////////////////////////////////////////////
// ФУНКЦИЯ TEST()
////////////////////////////////////////////////////////////////////////////////
class PriceTest extends MyTest
{

    public function test(array $args)
    {
        // получаем аргументы
        $price_param = $args[0];
        $markup_param = $args[1];
        $correct_answer = $args[2];
        // вычисляем правильный ответ
        $price = new Price($price_param, $markup_param);
        $answer = $price->getPrice();
        // сравниваем получившийся ответ с правильным
        if ($answer == $correct_answer) {
            $result = "<span style='color:green'>";
            $result .= 'Тест успешно пройден!';
            $result .= "</span>";
        } else {
            $result = "<span style='color:red'>";
            $result .= 'Тест не пройден!';
            $result .= "</span>";
        }
        return $result;
    }
}

////////////////////////////////////////////////////////////////////////////////
// MAIN
////////////////////////////////////////////////////////////////////////////////
$priceTest = new PriceTest();
$priceTest->setBankOfTests($tests);
$priceTest();