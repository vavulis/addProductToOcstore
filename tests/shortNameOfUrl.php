<?php

use vavulis\MyTest;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/test/MyTest.php';

// /upload/iblock/385/385aadcafcdedc394201fb3bd0caa69d.jpg -> 385aadcafcdedc394201fb3bd0caa69d.jpg
////////////////////////////////////////////////////////////////////////////////
// ТЕСТЫ. test($manufacturer_name, $correct_answer)
////////////////////////////////////////////////////////////////////////////////
$tests = [
    [
        '/upload/iblock/385/385aadcafcdedc394201fb3bd0caa69d.jpg',
        '385aadcafcdedc394201fb3bd0caa69d.jpg'
    ],
    [
        '385aadcafcdedc394201fb3bd0caa69d.jpg',
        '385aadcafcdedc394201fb3bd0caa69d.jpg'
    ],
    [
        '/upload/iblock/385/385aadcafcdedc394201fb3bd0caa69d.jpg/',
        '385aadcafcdedc394201fb3bd0caa69d.jpg'
    ],
    [
        '/385aadcafcdedc394201fb3bd0caa69d.jpg/',
        '385aadcafcdedc394201fb3bd0caa69d.jpg'
    ],
    [
        '385aadcafcdedc394201fb3bd0caa69d.jpg/',
        '385aadcafcdedc394201fb3bd0caa69d.jpg'
    ],
    [
        '123',
        '123'
    ],
    [
        123,
        null
    ],
    [
        '/',
        null
    ]
];

////////////////////////////////////////////////////////////////////////////////
// ФУНКЦИЯ TEST()
////////////////////////////////////////////////////////////////////////////////
class ShortNameOfUrlTest extends MyTest
{

    function getShortNameOfUrl($url)
    {
        // $url должен быть строкой
        if ((!is_string($url)) || ($url == '/') || (strlen($url)==0)) {
            return null;
        }
        
        $rest = substr($url, -1);
        if ($rest === '/') {
            $url = substr($url, 0, -1);
        }

        $tt = explode('/', $url);
        return $tt[count($tt) - 1];
    }

    public function test(array $args)
    {
        // получаем аргументы
        $url = $args[0];
        $correct_answer = $args[1];
        // вычисляем правильный ответ
        $answer = $this->getShortNameOfUrl($url);
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
$shortNameOfUrlTest = new ShortNameOfUrlTest();
$shortNameOfUrlTest->setBankOfTests($tests);
$shortNameOfUrlTest();
