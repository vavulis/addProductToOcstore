<?php
header('Content-Type: text/html; charset=utf-8');
// Подключаемся к базе данных
require 'config.php';
// Подключаем RedBeanPhp
require 'rb/rb-mysql.php';
R::setup( "mysql:host=$dbHost;dbname=$dbName", "$dbLogin", "$dbPassword" );
// Проверяем, проходит ли соединение с базой
if ( !R::testConnection() ) {
    exit('Нет подключения к базе данных');
}
else 
    echo "К базе подключилось норм! <br>";

// Тут происходит самое интересное --------------------------------
require 'Product.php';
$product = new Product();

// Заполняем основные поля товара
$language_id = 1;
$name = 'Евангельские беседы на каждый день года по церковным зачалам';
$description = '&lt;p&gt;Книга «Евангельские беседы на каждый день» включает в себя толкования на Евангелие святителя Иоанна Златоустого, блаженного Феофилакта Болгарского, святителя Феофана Затворника и других святых отцов, а также подробные исторические комментарии о быте, законах и обычаях современников земной жизни Спасителя. Толкования расположены по ежедневным Евангельским чтениям (церковным зачалам).&lt;/p&gt;\r\n\r\n&lt;p&gt;Издание будет интересно всем православным христианам: и мирянам — для чтения дома, в семье, и священнослужителям — для произнесения проповедей, и учащимся церковных учебных заведений.&lt;/p&gt;\r\n\r\n&lt;p&gt;Рекомендовано к публикации Издательским Советом Русской Православной Церкви.&lt;/p&gt;\r\n';
$tag = '';
$meta_title = 'Евангельские беседы на каждый день года по церковным зачалам. Православные книги почтой в магазине http://shop.konstantinsemenov.com';
$meta_h1 = 'Евангельские беседы на каждый день года по церковным зачалам';
$meta_description = 'Книга «Евангельские беседы на каждый день» включает в себя толкования на Евангелие святителя Иоанна Златоустого, блаженного Феофилакта Болгарского, святителя Феофана Затворника и других святых отцов, а также подробные исторические комментарии о быте, зако';
$meta_keyword='Евангелие, Иоанн Златоуст, Толкования';
$product->addDescriptionToProduct($language_id, $name, $description, $tag, $meta_title, $meta_h1, $meta_description, $meta_keyword);
print_r($product->descriptions);

// Добавляем картинки
$images = array(
    'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-1.jpg',
    'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-2.jpg',
    'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-3.jpg',
    'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-4.jpg'
);
$product->addImagesToProduct($images);
print_r($product->images);

// Назначаем товару категории
$categories = array(
    75
);
$product->addCategoriesToProduct($categories);
print_r($product->categories);

// закончилось ----------------------------------------------------

// Отключаемся от базы
R::close();
?>