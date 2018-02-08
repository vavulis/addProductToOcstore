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
$str .= 'description=<p>четкие колеса</p>';
setPostFromString($str);

for ($i = 0; $i < 50000; $i++) {
    $product = new Product($dbHost, $dbLogin, $dbPassword, $dbName);
    $product();
    unset($product);
}

echo '<p>Время выполнения скрипта: ' . round(microtime(true) - $start, 4) . ' сек.</p>';


