<?php

use vavulis\MyTest;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

define('__ROOT__', dirname(dirname(__FILE__)));

require_once __ROOT__ . '/vendor/vavulis/test/MyTest.php';


// $_POST['images'] = 'img1.jpg|img2.jpg|img3.jpg|img4.jpg';
// $image = getImageFromImages($_POST['images']);
// echo $image; // img1.jpg
// 
////////////////////////////////////////////////////////////////////////////////
// ТЕСТЫ. test($manufacturer_name, $correct_answer)
////////////////////////////////////////////////////////////////////////////////
$tests = [
    [
        ' img1.jpg | img2.jpg|img3.jpg|img4.jpg ',
        'img1.jpg'
    ],
    [
        ' img1.jpg |',
        'img1.jpg'
    ],
    [
        '| img1.jpg',
        null
    ],
    [
        '|img1.jpg |',
        null
    ],
    [
        '',
        null
    ],
    [
        '|',
        null
    ],
    [
        '|||',
        null
    ],
    [
        'img1%.jpg|img2.jpg|img3.jpg|img4.jpg',
        null
    ],
    [
        'img1.jpg|img%2.jpg|img3.jpg|img4.jpg',
        'img1.jpg'
    ],
    [
        '123123',
        '123123'
    ]
];

////////////////////////////////////////////////////////////////////////////////
// ФУНКЦИЯ TEST()
////////////////////////////////////////////////////////////////////////////////
class GetImageFromImagesTest extends MyTest
{

    private function isBadSymbols($bad_symbols, $str)
    {
        $str_array = str_split($str);
        foreach ($bad_symbols as $bs) {
            foreach ($str_array as $sa) {
                if ($bs === $sa) {
                    return true;
                }
            }
        }
        return false;
    }

    function getImageFromImagesTest($images)
    {
        if (!is_string($images)) {
            return null;
        }
        $images = str_replace(' ', '', $images);
        if (($images === '|') || ($images === '')) {
            return null;
        }
        $tt = explode('|', $images);
        $image = $tt[0];
        if (($image === '') || ($this->isBadSymbols(['$', '#', ';', '!', ',', '@', '%', '~', '`', ':', '&', '<', '>', '[', ']', '/', '\\'], $image))) {
            return null;
        }
        return $image;
    }

    public function test(array $args)
    {
        // получаем аргументы
        $images = $args[0];
        $correct_answer = $args[1];
        // вычисляем правильный ответ
        $answer = $this->getImageFromImagesTest($images);
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
$test = new GetImageFromImagesTest();
$test->setBankOfTests($tests);
$test();
