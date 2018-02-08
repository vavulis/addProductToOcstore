<?php

use vavulis\MyTest;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Attributes.php';
require_once __ROOT__ . '/vendor/vavulis/test/MyTest.php';



////////////////////////////////////////////////////////////////////////////////
// ИНИЦИАЛИЗИРУЕМ НАЧАЛЬНЫЕ ПАРАМЕТРЫ
////////////////////////////////////////////////////////////////////////////////
$dbh = new PDO("mysql:host=127.0.0.1;dbname=semenoh3_tst;charser=utf8", 'semenoh3_tst', '123456', array(
    PDO::ATTR_PERSISTENT => true, // храним соединение, чтобы не пересоздавать его для каждого товара
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
    ));

$array_of_attribute = [
    new Attribute('Характеристики', 7, 'ширина', 100),
    new Attribute('Характеристики', 7, 'высота', 101),
    new Attribute('Характеристики', 7, 'длина', 102),
    new Attribute('Состав', 7, 'витамины', 103),
    new Attribute('Состав', 7, 'вредные компоненты', 104)
];

$product_id = 85;

$attributes = new Attributes($dbh, $product_id, $array_of_attribute);
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//            ПОДГОТАВЛИВАЕМ ПАРАМЕТРЫ ДЛЯ ТЕСТИРОВАНИЯ метода класса
//                   Attributes->findSimilarAttributeTest()
////////////////////////////////////////////////////////////////////////////////
$find_similar_attribute_args = [
    [
        $attributes, 'Характеристики', 'цвет', [
            'status' => 'findGroup',
            'data' => [
                'group_id' => 7
            ]
        ]
    ],
    [
        $attributes, 'Характеристики', 'Цвет', [
            'status' => 'findGroup',
            'data' => [
                'group_id' => 7
            ]
        ]
    ],
    [
        $attributes, 'Характеристики', 'ширина', [
            'status' => 'findAtribute',
            'data' => [
                'group_id' => 7,
                'name_id' => 100
            ]
        ]
    ],
    [
        $attributes, 'Характеристики', 'Ширина', [
            'status' => 'findAtribute',
            'data' => [
                'group_id' => 7,
                'name_id' => 100
            ]
        ]
    ],
    [
        $attributes, 'Потроха', 'ширина', [
            'status' => 'findNothing',
            'data' => []
        ]
    ],
    [
        $attributes, 'Группа', '', [
            'status' => 'error',
            'data' => [
                'msg' => 'не заполнено имя атрибута!'
            ]
        ]
    ],
    [
        $attributes, '', 'Атрибут', [
            'status' => 'error',
            'data' => [
                'msg' => 'не заполнена группа атрибутов!'
            ]
        ]
    ]
];

class FindSimilarAttributeTest extends MyTest
{

    public function test(array $args)
    {
        // получаем аргументы
        $attributes = $args[0];
        $group = $args[1];
        $name = $args[2];
        $correct_answer = $args[3];
        // вычисляем правильный ответ
        $answer = $attributes->findSimilarAttribute($group, $name);
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
////////////////////////////////////////////////////////////////////////////////
//                  ТЕСТИРУЕМ Attributes->findSimilarAttributeTest()
////////////////////////////////////////////////////////////////////////////////
//$findSimilarAttributeTest = new FindSimilarAttributeTest($find_similar_attribute_args);
//$findSimilarAttributeTest();
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//                  ТЕСТИРУЕМ Attributes->setAttributesToProduct()
////////////////////////////////////////////////////////////////////////////////
$from_post = [
    [
        'group' => 'Характеристики',
        'name' => 'ветер',
        'val' => '123'
    ],
    [
        'group' => 'Характеристики',
        'name' => 'ширина',
        'val' => '10 грамм'
    ],
    [
        'group' => 'Просто группа',
        'name' => 'вагон',
        'val' => "hj"
    ]
];

$attributes2 = new Attributes($dbh, $product_id);

$attributes2->setAttributesToProduct($dbh, $product_id, $from_post);
////////////////////////////////////////////////////////////////////////////////






