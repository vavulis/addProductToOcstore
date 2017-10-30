<?php

/**
 * Класс ТОВАР
 * @author Konstantin Semenov (vavulis)
 */
class Product {

    // Поля таблицы oc_product
    var $product_id; // ''
    var $model; // '7938'
    var $sku; // ''
    var $upc; // ''
    var $ean; // ''
    var $jan; // ''
    var $isbn; // '978-5-94759-201-6'
    var $mpn; // ''
    var $location; // ''
    var $quantity; // 6
    var $stock_status_id; // 7
    var $image; // 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg'
    var $manufacturer_id; // 26
    var $shipping; // 1
    var $price; // '419.0000'
    var $points; // 0
    var $tax_class_id; // 0
    var $date_available; // ''
    var $weight; // '550.00'
    var $weight_class_id; // 2
    var $length; // '12.50'
    var $width; // '17.00'
    var $height; // '4.00'
    var $length_class_id; // 1
    var $subtract; // 1
    var $minimum; // 1
    var $sort_order; // 1
    var $status; // 1
    var $viewed; // 0
    var $date_added; // now()
    var $date_modified; // '0000-00-00 00:00:00'
    // Описание товара
//    $this->descriptions[$language_id] = array(
//            "name" => $name,
//            "description" => $description,
//            "tag" => $tag,
//            "meta_title" => $meta_title,
//            "meta_h1" => $meta_h1,
//            "meta_description" => $meta_description,
//            "meta_keyword" => $meta_keyword
//        );
    var $descriptions = [];
    // Картинки товара.
    var $images = [];
    // Основная иерархия категорий товара, хлебные крошки
    var $categories = [];
    // alias для категории для seopro
    var $category_alias_seopro = '';
    // alias для товара для seopro
    var $product_alias_seopro = '';
    // Атрибуты товара и их значения. [ 'atr1' => 10, ... ]
    var $attributes = [];
    // Группы атрибутов. [ 'group_id1' => 'group_name1', ... ]
    var $groups_of_attributes = [];
    // Файл логов
    var $log_file = 'logs/messages.log';
    // Файл логов ошибок
    var $error_file = 'logs/errors.log';

    // Конструктор класса
    function __construct() {
        // Задаем основные параметры товара
        $this->getParamsFromPost();
    }

    // $attributes = "Артикул:13497|Код товара:94017|Дата поступления:29.09.2017|Издательство: Правило веры, Москва"
    function addAttributesToProduct($attributes) {
        $attributes = explode("|", $attributes);
        foreach ($attributes as $attribute) {
            $t = explode(":", $attribute);
            // если есть пара ключ:значение, то добавляем в атрибуты
            if (count($t) == 2)
                $this->attributes[trim($t[0])] = trim($t[1]);
        }
    }

    function addProductToDB() {
        // надо сохранить product_id
//        INSERT INTO `oc_product` (`product_id`, `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) VALUES
//('', '7938', '', '', '', '', '978-5-94759-201-6', '', '', 6, 7, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg', 26, 1, '419.0000', 0, 0, '', '550.00', 2, '12.50', '17.00', '4.00', 1, 1, 1, 1, 1, 0, now(), '0000-00-00 00:00:00');
    }

    function addDescriptionToDB() {
        echo "addDescriptionToDB();<br>";
    }

    function addImagesToDB() {
        echo "addImagesToDB();<br>";
    }

    function addLayoutToDB() {
        echo "addLayoutToDB();<br>";
    }

    function addMagazineToDB() {
        echo "addMagazineToDB();<br>";
    }

    // создаем категории, добавляем описания, регистрируем категории в магазине, если надо добавляем алиасы к категориям в сео-про (можно и руками, категорий не много)
    function addCategoryToDB() {
        echo "addCategoryToDB();<br>";
    }

    // назначаем товару категории
    function setCategoriesToDB() {
        echo "setCategoriesToDB();<br>";
    }

    function addAttributesGroupToDB() {
        echo "addAttributesGroupToDB();<br>";
    }

    function addAttributesToDB() {
        echo "addAttributesToDB();<br>";
    }

    function setAttributesToDB() {
        echo "setAttributesToDB();<br>";
    }

    // задать алиас для продукта в seopro
    function setProductAliasToDB() {
        echo "setProductAliasToDB();<br>";
    }

    // Заполняем свойства класса из массива $_POST
    function getParamsFromPost() {
        $this->model = mysql_real_escape_string($_POST[model]);
        $this->sku = mysql_real_escape_string($_POST[sku]);
        $this->upc = mysql_real_escape_string($_POST[upc]);
        $this->ean = mysql_real_escape_string($_POST[ean]);
        $this->jan = mysql_real_escape_string($_POST[jan]);
        $this->isbn = mysql_real_escape_string($_POST[isbn]);
        $this->mpn = mysql_real_escape_string($_POST[mpn]);
        $this->location = mysql_real_escape_string($_POST[location]);
        $this->quantity = mysql_real_escape_string($_POST[quantity]);
        $this->stock_status_id = mysql_real_escape_string($_POST[stock_status_id]);
        $this->image = mysql_real_escape_string($_POST[image]);
        $this->manufacturer_id = mysql_real_escape_string($_POST[manufacturer_id]);
        $this->shipping = mysql_real_escape_string($_POST[shipping]);
        $this->price = mysql_real_escape_string($_POST[price]);
        $this->points = mysql_real_escape_string($_POST[points]);
        $this->tax_class_id = mysql_real_escape_string($_POST[tax_class_id]);
        $this->date_available = mysql_real_escape_string($_POST[date_available]);
        $this->weight = mysql_real_escape_string($_POST[weight]);
        $this->weight_class_id = mysql_real_escape_string($_POST[weight_class_id]);
        $this->width = mysql_real_escape_string($_POST[width]);
        $this->height = mysql_real_escape_string($_POST[height]);
        $this->length_class_id = mysql_real_escape_string($_POST[length_class_id]);
        $this->subtract = mysql_real_escape_string($_POST[subtract]);
        $this->minimum = mysql_real_escape_string($_POST[minimum]);
        $this->sort_order = mysql_real_escape_string($_POST[sort_order]);
        $this->status = mysql_real_escape_string($_POST[status]);
        $this->viewed = mysql_real_escape_string($_POST[viewed]);
        $this->date_added = mysql_real_escape_string($_POST[date_added]);
        $this->date_modified = mysql_real_escape_string($_POST[date_modified]);
    }

    function log($text) {
        // пишем лог в файл $this->log_file
        $fp = fopen($this->log_file, "a"); // Открываем файл в режиме записи
        $test = fwrite($fp, date("Y-m-d H:i:s") . " # " . $_SERVER['REMOTE_ADDR'] . " # $text\r\n");
        if ($test)
            echo 'Данные в файл успешно занесены.';
        else
            echo 'Ошибка при записи в файл.';
        fclose($fp); //Закрытие файла
    }

    function errorLog($text) {
        // пишем лог в файл $this->errors_log
        $fp = fopen($this->error_file, "a"); // Открываем файл в режиме записи
        $test = fwrite($fp, date("Y-m-d H:i:s") . " # " . $_SERVER['REMOTE_ADDR'] . " # $text\r\n");
        if ($test)
            echo 'Данные в файл успешно занесены.';
        else
            echo 'Ошибка при записи в файл.';
        fclose($fp); //Закрытие файла
    }

    function checkParams() {
        echo "checkParams();<br>";
    }

    function mainPotok() {
        echo "mainPotok();<br>";
        $this->checkParams();
        $this->addProductToDB();
        $this->addDescriptionToDB();
        $this->addLayoutToDB();
        $this->addCategoryToDB();
        $this->setCategoriesToDB();
        $this->addAttributesGroupToDB();
        $this->addAttributesToDB();
        $this->setAttributesToDB();
    }

}

?>