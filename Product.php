<?php
require_once 'MyException.php';

/**
 * Класс ТОВАР
 * @author Konstantin Semenov (vavulis)
 */
class Product
{

    // БД
    var $db; // Дескриптор подключения к БД
    var $dbHost = '127.0.0.1';
    var $dbLogin = 'semenoh3_tst';
    var $dbPassword = '123456';
    var $dbName = 'semenoh3_tst';
    // Поля таблицы oc_product
    var $product_id = ''; // ''
    var $model; // '7938'
    var $sku = ''; // ''
    var $upc = ''; // ''
    var $ean = ''; // ''
    var $jan = ''; // ''
    var $isbn = ''; // '978-5-94759-201-6'
    var $mpn = ''; // ''
    var $location = ''; // ''
    var $quantity; // 6
    var $stock_status_id = 7; // 7
    var $image; // 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg'
    var $manufacturer_id = 26; // 26
    var $shipping = 1; // 1
    var $price; // '419.0000'
    var $points = 0; // 0
    var $tax_class_id = 0; // 0
    var $date_available = ''; // ''
    var $weight = '550.00'; // '550.00'
    var $weight_class_id = 2; // 2
    var $length = '12.50'; // '12.50'
    var $width = '17.00'; // '17.00'
    var $height = '4.00'; // '4.00'
    var $length_class_id = 1; // 1
    var $subtract = 1; // 1
    var $minimum = 1; // 1
    var $sort_order = 1; // 1
    var $status = 1; // 1
    var $viewed = 0; // 0
    var $date_added; // now()
    var $date_modified = '0000-00-00 00:00:00'; // '0000-00-00 00:00:00'
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
    // Язык
    var $language_id = 1; // 1 - Русский
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

    function prepareDB()
    {
        // Подключается к БД
        $this->db = mysqli_connect($this->dbHost, $this->dbLogin, $this->dbPassword);
        if (!$this->db) {
            throw new MyException('Mysql error. Cannot connect to DB!');
        }
        mysqli_query("SET NAMES utf8");
    }

    // Конструктор класса
    function __construct()
    {
        // Подключаемся к базе
        $this->prepareDB();
        
        // Задаем основные параметры товара
        $this->prepareParams();
    }

    // $attributes = "Артикул:13497|Код товара:94017|Дата поступления:29.09.2017|Издательство: Правило веры, Москва"
    function addAttributesToProduct($attributes)
    {
        $tt = explode("|", $attributes);
        foreach ($tt as $attribute) {
            $t = explode(":", $attribute);
            // если есть пара ключ:значение, то добавляем в атрибуты
            if (count($t) == 2) {
                $this->attributes[trim($t[0])] = trim($t[1]);
            }
        }
    }

    function addProductToDB()
    {
        echo "addProductToDB()";
        // надо сохранить product_id

        $query = "INSERT INTO oc_product (product_id, model, sku, upc, ean, jan, isbn, mpn, location, quantity, stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, viewed, date_added, date_modified) VALUES ('$this->product_id', '$this->model', '$this->sku', '$this->upc','$this->ean','$this->jan','$this->isbn','$this->mpn','$this->location','$this->quantity','$this->stock_status_id','$this->image','$this->manufacturer_id','$this->shipping','$this->price','$this->points','$this->tax_class_id','$this->date_available','$this->weight','$this->weight_class_id','$this->length','$this->width','$this->height','$this->length_class_id','$this->subtract','$this->minimum','$this->sort_order','$this->status','$this->viewed',now(),'$this->date_modified')";
        mysqli_query($query) or die('Не удалось создать товар в запросе Mysql::' . print_r(error_get_last()));

        return $this;
    }

    function addDescriptionToDB()
    {
        echo "addDescriptionToDB();<br>";
        return $this;
    }

    function addImagesToDB()
    {
        echo "addImagesToDB();<br>";
        return $this;
    }

    function addLayoutToDB()
    {
        echo "addLayoutToDB();<br>";
        return $this;
    }

    function addMagazineToDB()
    {
        echo "addMagazineToDB();<br>";
        return $this;
    }

    // создаем категории, добавляем описания, регистрируем категории в магазине, если надо добавляем алиасы к категориям в сео-про (можно и руками, категорий не много)
    function addCategoryToDB()
    {
        echo "addCategoryToDB();<br>";
        return $this;
    }

    // назначаем товару категории
    function setCategoriesToDB()
    {
        echo "setCategoriesToDB();<br>";
        return $this;
    }

    function addAttributesGroupToDB()
    {
        echo "addAttributesGroupToDB();<br>";
        return $this;
    }

    function addAttributesToDB()
    {
        echo "addAttributesToDB();<br>";
        return $this;
    }

    function setAttributesToDB()
    {
        echo "setAttributesToDB();<br>";
        return $this;
    }

    // задать алиас для продукта в seopro
    function setProductAliasToDB()
    {
        echo "setProductAliasToDB();<br>";
        return $this;
    }

    // Заполняем свойства класса из массива $_POST
    function prepareParams()
    {
        // Общие параметры товара        
        if (isset($_POST[model])) {
            $this->model = $_POST[model];
        }
        if (isset($_POST[sku])) {
            $this->sku = $_POST[sku];
        }
        if (isset($_POST[upc])) {
            $this->upc = $_POST[upc];
        }
        if (isset($_POST[ean])) {
            $this->ean = $_POST[ean];
        }
        if (isset($_POST[jan])) {
            $this->jan = $_POST[jan];
        }
        if (isset($_POST[isbn])) {
            $this->isbn = $_POST[isbn];
        }
        if (isset($_POST[mpn])) {
            $this->mpn = $_POST[mpn];
        }
        if (isset($_POST[location])) {
            $this->location = $_POST[location];
        }
        if (isset($_POST[quantity])) {
            $this->quantity = $_POST[quantity];
        }
        if (isset($_POST[stock_status_id])) {
            $this->stock_status_id = $_POST[stock_status_id];
        }
        if (isset($_POST[image])) {
            $this->image = $_POST[image];
        }
        if (isset($_POST[manufacturer_id])) {
            $this->manufacturer_id = $_POST[manufacturer_id];
        }
        if (isset($_POST[shipping])) {
            $this->shipping = $_POST[shipping];
        }
        if (isset($_POST[price])) {
            $this->price = $_POST[price];
        }
        if (isset($_POST[points])) {
            $this->points = $_POST[points];
        }
        if (isset($_POST[tax_class_id])) {
            $this->tax_class_id = $_POST[tax_class_id];
        }
        if (isset($_POST[date_available])) {
            $this->date_available = $_POST[date_available];
        }
        if (isset($_POST[weight])) {
            $this->weight = $_POST[weight];
        }
        if (isset($_POST[weight_class_id])) {
            $this->weight_class_id = $_POST[weight_class_id];
        }

        if (isset($_POST[width])) {
            $this->width = $_POST[width];
        }
        if (isset($_POST[height])) {
            $this->height = $_POST[height];
        }
        if (isset($_POST[length_class_id])) {
            $this->length_class_id = $_POST[length_class_id];
        }
        if (isset($_POST[subtract])) {
            $this->subtract = $_POST[subtract];
        }
        if (isset($_POST[minimum])) {
            $this->minimum = $_POST[minimum];
        }
        if (isset($_POST[sort_order])) {
            $this->sort_order = $_POST[sort_order];
        }
        if (isset($_POST[status])) {
            $this->status = $_POST[status];
        }
        if (isset($_POST[viewed])) {
            $this->viewed = $_POST[viewed];
        }
        if (isset($_POST[date_added])) {
            $this->date_added = $_POST[date_added];
        }
        if (isset($_POST[date_modified])) {
            $this->date_modified = $_POST[date_modified];
        }
        // Описание товара
        $this->descriptions[$this->language_id] = array(
            "name" => $_POST[name],
            "description" => $_POST[description],
            "tag" => '',
            "meta_title" => '',
            "meta_h1" => '',
            "meta_description" => '',
            "meta_keyword" => ''
        );
        return $this;
    }

    function log($text)
    {
        // пишем лог в файл $this->log_file
        $fp = fopen($this->log_file, "a"); // Открываем файл в режиме записи
        $test = fwrite($fp, date("Y-m-d H:i:s") . " # " . $_SERVER['REMOTE_ADDR'] . " # $text\r\n");
        if ($test)
            echo 'Данные в файл успешно занесены.';
        else
            echo 'Ошибка при записи в файл.';
        fclose($fp); //Закрытие файла
    }

    function errorLog($text)
    {
        // пишем лог в файл $this->errors_log
        $fp = fopen($this->error_file, "a"); // Открываем файл в режиме записи
        $test = fwrite($fp, date("Y-m-d H:i:s") . " # " . $_SERVER['REMOTE_ADDR'] . " # $text\r\n");
        if ($test)
            echo 'Данные в файл успешно занесены.';
        else
            echo 'Ошибка при записи в файл.';
        fclose($fp); //Закрытие файла
    }

    function checkParams()
    {
        echo "checkParams();<br>";
        // Имя
        if ($this->descriptions[$this->language_id]['name'] == '') {
            throw new MyException("Не указано имя. name=$this->name<br>");
        }
        // Модель
        if ($this->model == '') {
            throw new MyException("Не указана модель. model=$this->model");
        }
        return $this;
    }

    function mainPotok()
    {
        echo "mainPotok();<br>";
        $this->checkParams()->addProductToDB()->addDescriptionToDB()->addLayoutToDB()->addCategoryToDB()->setCategoriesToDB()->addAttributesGroupToDB()->addAttributesToDB()->setAttributesToDB();
    }

    public function __invoke()
    {
        $this->mainPotok();
    }
}
