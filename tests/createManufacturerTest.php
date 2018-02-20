<?php

use vavulis\MyTest;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Manufacturers.php';
require_once __ROOT__ . '/vendor/vavulis/test/MyTest.php';


////////////////////////////////////////////////////////////////////////////////
// ТЕСТЫ. test($manufacturer_name, $correct_answer)
////////////////////////////////////////////////////////////////////////////////
$tests = [
    [
        'Mina Brand',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 11
        ]
    ],
    [
        '',
        [
            'status' => 'error',
            'msg' => 'Не задано название производителя!'
        ]
    ],
    [
        '123',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 12
        ]
    ],
    [
        '12;$a3',
        [
            'status' => 'error',
            'msg' => 'В имени производителя есть недопустимые символы!'
        ]
    ],
    [
        'Mina Brand',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 11
        ]
    ]
];

$tests2 = [
    [
        'Mina Brand',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 11
        ]
    ],
    [
        '',
        [
            'status' => 'error',
            'msg' => 'Не задано название производителя!'
        ]
    ],
    [
        '123',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 12
        ]
    ],
    [
        '12;a3',
        [
            'status' => 'error',
            'msg' => 'В имени производителя есть недопустимые символы!'
        ]
    ],
    [
        'Mina Brand2',
        [
            'status' => 'ok',
            'id_of_created_manufacturer' => 13
        ]
    ]
];

////////////////////////////////////////////////////////////////////////////////
// ФУНКЦИЯ TEST()
////////////////////////////////////////////////////////////////////////////////
class CreateManufacturerTest extends MyTest
{

    public $dbh;
    public $brands;

    public function __construct()
    {
        parent::__construct();
        $this->dbh = new PDO("mysql:host=127.0.0.1;dbname=semenoh3_tst;charser=utf8", 'semenoh3_tst', '123456', array(
            PDO::ATTR_PERSISTENT => true, // храним соединение, чтобы не пересоздавать его для каждого товара
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ));
        $this->brands = new Manufacturers($this->dbh);
    }

    public function test(array $args)
    {
        // получаем аргументы
        $name = $args[0];
        $correct_answer = $args[1];
        // вычисляем правильный ответ
        $answer = $this->brands->createManufacturer($this->dbh, $name);
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
$brandsTest = new CreateManufacturerTest();
$brandsTest->setBankOfTests($tests);
$brandsTest();
$brandsTest->setBankOfTests($tests2);
$brandsTest();

