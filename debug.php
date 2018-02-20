<?php
ini_set('max_execution_time', 0);
define('__ROOT__', __DIR__);
header('Content-Type: text/html; charset=utf-8');
$start = microtime(true);
require_once __ROOT__ . '/config.php';
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Product.php';

function setPostFromString($str)
{
    $x2 = explode('&', $str);
    foreach ($x2 as $tt) {
        $x3 = explode('=', $tt);
        $key = trim($x3[0]);
        $val = trim($x3[1]);
        $_POST[$key] = $val;
    }
}
$str = 'name=Кросовки Adidas&';
$str .= 'price=419.0000&';
$str .= 'model=123456&';
$str .= 'categories=a|b|c|d|e&';
$str .= 'image=catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg&';
$str .= 'images=img1.jpg|img2.jpg|img3.jpg|img4.jpg&';
$str .= 'id_of_groups_of_attributes=5&';
$str .= 'attributes=характеристики:цвет:красный|характеристики:пол:унисекс|характеристики:размер:45&';
$str .= 'manufacturer=МинаДанил бренд&';
$str .= 'description=<p>четкие колеса</p>';
setPostFromString($str);

//for ($i = 0; $i < 50000; $i++) {
//    $product = new Product($dbHost, $dbLogin, $dbPassword, $dbName);
//    $product();
//    unset($product);
//}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

for ($i = 0; $i < 10; $i++) {
    $name = generateRandomString();
    $price = rand(10, 5000);
    $model = 'art_number_' . $i;
    $categories = [];
    for ($j=0; $j<rand(1,5); $j++) {
        $categories[] = generateRandomString(rand(1,5));       
    }
    $categories = implode('|', $categories);
    $product = new Product($dbHost, $dbLogin, $dbPassword, $dbName);
    $product();
    unset($product);
    var_dump($name);
    var_dump($price);
    var_dump($model);
    var_dump($categories);    
}

echo '<p>Время выполнения скрипта: ' . round(microtime(true) - $start, 4) . ' сек.</p>';


