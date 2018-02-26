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
$str .= 'price=419&';
$str .= 'model=123456&';
$str .= 'categories=a|b|c|d|e&';
$str .= 'image=&';
$str .= 'images=img1.jpg|img2.jpg|img3.jpg|img4.jpg&';
$str .= 'id_of_groups_of_attributes=5&';
$str .= 'attributes=характеристики:цвет:красный|характеристики:пол:унисекс|характеристики:размер:45&';
$str .= 'manufacturer=МинаДанил бренд&';
$str .= 'description=<p>четкие колеса</p>';
//setPostFromString($str);

//for ($i = 0; $i < 50000; $i++) {
//    $product = new Product($dbHost, $dbLogin, $dbPassword, $dbName);
//    $product();
//    unset($product);
//}

function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}
//for ($i = 0; $i < 10; $i++) {
//    $name = generateRandomString();
//    $price = rand(10, 5000);
//    $model = 'art_number_' . $i;
//    $categories = [];
//    for ($j=0; $j<rand(1,5); $j++) {
//        $categories[] = generateRandomString(rand(1,5));       
//    }
//    $categories = implode('|', $categories);
//    $product = new Product($dbHost, $dbLogin, $dbPassword, $dbName);
//    $product();
//    unset($product);
//    var_dump($name);
//    var_dump($price);
//    var_dump($model);
//    var_dump($categories);    
//}

$post = 'a:7:{s:4:"name";s:31:"Сумка Michael Kors 28001-7";s:5:"model";s:4:"QIBD";s:5:"price";s:5:"1 100";s:10:"categories";s:40:"Брендовые сумки оптом";s:5:"image";s:0:"";s:6:"images";s:223:"/upload/iblock/115/1153385abc4eac718579cbfdbcbf8de2.jpg|/upload/iblock/3bb/3bb019171592512024f5ce6ff88066be.jpg|/upload/iblock/3b6/3b6e5afa205150077ab2a7ad6ae6b132.jpg|/upload/iblock/bf4/bf4ff51acfaa7a935a7c3e0c728f31fc.jpg";s:11:"description";s:42:"размеры 31*24*11 ручки 16 см";}';
$_POST = unserialize($post);

$product = new Product($dbHost, $dbLogin, $dbPassword, $dbName, $oc_version, $language_id);
$product();

echo '<p>Время выполнения скрипта: ' . round(microtime(true) - $start, 4) . ' сек.</p>';


