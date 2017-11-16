<?php
//namespace vavulis\parserOpencart;
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/logs/MyLog.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Categories.php';

/**
 * Класс ТОВАР
 * @author Konstantin Semenov (vavulis)
 */
class Product
{

    // БД
    var $dbh; // Дескриптор подключения к БД
    var $dbHost = '';
    var $dbLogin = '';
    var $dbPassword = '';
    var $dbName = '';
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
    var $quantity = 1; // 6
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
    // Картинки товара. Массив названий картинок
    var $images = [];
    // Префикс путей картинок
    var $images_prefix = "catalog/images/";
    // Все категории из БД
    var $all_categories = [];
    // $_POST['categories'] = 'cat1|cat2|cat3';
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
    var $log_file = __ROOT__ . '/logs/messages.log';
    // Файл логов ошибок
    var $error_file = __ROOT__ . '/logs/error.log';

    function prepareDB($dbHost, $dbLogin, $dbPassword, $dbName)
    {

        // Подключается к БД
        try {
            $this->dbh = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName;charser=utf8", $this->dbLogin, $this->dbPassword, array(
                PDO::ATTR_PERSISTENT => true, // храним соединение, чтобы не пересоздавать его для каждого товара
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ));
        } catch (PDOException $ex) {
            MyLog::log('Подключение к базе данных не удалось: ' . $ex->getMessage(), $this->error_file);
            die('Подключение к базе данных не удалось!');
        }
    }

    // Конструктор класса
    function __construct($dbHost, $dbLogin, $dbPassword, $dbName)
    {
        if (!isset($dbHost) || $dbHost == '') {
            throw new MyException("Ошибка в параметрах подключения к БД! Не задан ХОСТ!");
        }
        if (!isset($dbLogin) || $dbLogin == '') {
            throw new MyException("Ошибка в параметрах подключения к БД! Не задан ПОЛЬЗОВАТЕЛЬ!");
        }
        if (!isset($dbPassword) || $dbPassword == '') {
            throw new MyException("Ошибка в параметрах подключения к БД! Не задан ПАРОЛЬ!");
        }
        if (!isset($dbName) || $dbName == '') {
            throw new MyException("Ошибка в параметрах подключения к БД! Не задано ИМЯ ТАБЛИЦЫ!");
        }
        $this->dbHost = $dbHost;
        $this->dbLogin = $dbLogin;
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;

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

        $sql = "INSERT INTO oc_product (product_id, model, sku, upc, ean, jan, isbn, mpn, location, quantity, stock_status_id, image, manufacturer_id, shipping, price, points, tax_class_id, date_available, weight, weight_class_id, length, width, height, length_class_id, subtract, minimum, sort_order, status, viewed, date_added, date_modified)";
        $sql .= " VALUES (:product_id, :model, :sku, :upc, :ean, :jan, :isbn, :mpn, :location, :quantity, :stock_status_id, :image, :manufacturer_id, :shipping, :price, :points, :tax_class_id, :date_available, :weight, :weight_class_id, :length, :width, :height, :length_class_id, :subtract, :minimum, :sort_order, :status, :viewed, now(), :date_modified)";

        $stmt = $this->dbh->prepare($sql);
        $param = array(
            'product_id' => $this->product_id,
            'model' => $this->model,
            'sku' => $this->sku,
            'upc' => $this->upc,
            'ean' => $this->ean,
            'jan' => $this->jan,
            'isbn' => $this->isbn,
            'mpn' => $this->mpn,
            'location' => $this->location,
            'quantity' => $this->quantity,
            'stock_status_id' => $this->stock_status_id,
            'image' => $this->image,
            'manufacturer_id' => $this->manufacturer_id,
            'shipping' => $this->shipping,
            'price' => $this->price,
            'points' => $this->points,
            'tax_class_id' => $this->tax_class_id,
            'date_available' => $this->date_available,
            'weight' => $this->weight,
            'weight_class_id' => $this->weight_class_id,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'length_class_id' => $this->length_class_id,
            'subtract' => $this->subtract,
            'minimum' => $this->minimum,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'viewed' => $this->viewed,
            'date_modified' => $this->date_modified
        );

        try {
            $stmt->execute($param);
            $this->product_id = $this->dbh->lastInsertId();
            MyLog::log("Успешно добавлен ТОВАР №$this->product_id !", $this->log_file);
        } catch (PDOException $ex) {
            MyLog::log("Не удалось создать товар. " . $ex->getMessage() . "\t" . $sql . "\t" . $param, $this->error_file);
        }

        return $this;
    }

    function addDescriptionToDB()
    {
        echo "addDescriptionToDB();<br>";

        $sql = "INSERT INTO oc_product_description (product_id, language_id, name, description, tag, meta_title, meta_h1, meta_description, meta_keyword)";
        $sql .= " VALUES (:product_id, :language_id, :name, :description, :tag, :meta_title, :meta_h1, :meta_description, :meta_keyword)";
        $stmt = $this->dbh->prepare($sql);
        $param = array(
            'product_id' => $this->product_id,
            'language_id' => $this->language_id,
            'name' => $this->descriptions[$this->language_id]['name'],
            'description' => $this->descriptions[$this->language_id]['description'],
            'tag' => $this->descriptions[$this->language_id]['tag'],
            'meta_title' => $this->descriptions[$this->language_id]['meta_title'],
            'meta_h1' => $this->descriptions[$this->language_id]['meta_h1'],
            'meta_description' => $this->descriptions[$this->language_id]['meta_description'],
            'meta_keyword' => $this->descriptions[$this->language_id]['meta_keyword']
        );

        try {
            $stmt->execute($param);
            MyLog::log("Успешно добавлено ОПИСАНИЕ товара №$this->product_id !", $this->log_file);
        } catch (PDOException $ex) {
            MyLog::log("Не удалось создать описание товара №$this->product_id! " . $ex->getMessage() . "\t" . $sql . "\t" . $param, $this->error_file);
        }

        return $this;
    }

    function addImagesToDB()
    {
        echo "addImagesToDB();<br>";

        try {
            $sql = "INSERT INTO oc_product_image (product_image_id, product_id, image, sort_order)";
            $sql .= " VALUES ('', :product_id, :image, '')";
            $stmt = $this->dbh->prepare($sql);

            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':image', $image);
            foreach ($this->images as $t) {
                $image = $this->images_prefix . $t;
                $stmt->execute();
            }
            MyLog::log("Успешно добавлены КАРТИНКИ товара №$this->product_id !", $this->log_file);
        } catch (PDOException $ex) {
            MyLog::log("Не удалось создать картинки товара №$this->product_id! " . $ex->getMessage(), $this->error_file);
        }

        return $this;
    }

    function addLayoutToDB()
    {
        echo "addLayoutToDB();<br>";

        try {
            $sql = "INSERT INTO oc_product_to_layout (product_id, store_id, layout_id) VALUES (?, 0, 0);";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute(array($this->product_id));
            MyLog::log("Успешно добавили LAYOUT к товару №$this->product_id", $this->log_file);
        } catch (Exception $ex) {
            MyLog::log("Ошибка добавления LAYOUT. " . $ex->getMessage(), $this->error_file);
        }

        return $this;
    }

    function addMagazineToDB()
    {
        echo "addMagazineToDB();<br>";

        try {
            $sql = "INSERT INTO oc_product_to_store (product_id, store_id) VALUES (:product_id, 0);";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute(array($this->product_id));
            MyLog::log("Успешно назначили MAGAZINE к товару №$this->product_id", $this->log_file);
        } catch (Exception $ex) {
            MyLog::log("Ошибка добавления MAGAZINE. " . $ex->getMessage(), $this->error_file);
        }

        return $this;
    }

    function addCategoryToDB($name, $parent_id)
    {
        try {
            $this->dbh->beginTransaction();

            $sql = "INSERT INTO `oc_category` (`image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`)";
            $sql .= " VALUES ('', :parent_id, 1, 0, 1, 0, now(), now())";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $last_id1 = $this->dbh->lastInsertId();
            echo "Last id1= $last_id1 <br>";

            $sql2 = "INSERT INTO `oc_category_description` (`language_id`, `name`, `description`, `meta_title`, `meta_h1`, `meta_description`, `meta_keyword`)";
            $sql2 .= " VALUES (1, :name, '', '', '', '', '')";
            $stmt2 = $this->dbh->prepare($sql2);
            $stmt2->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt2->execute();
            $last_id2 = $this->dbh->lastInsertId();
            echo "Last id2 = $last_id2";

            $this->dbh->commit();

            MyLog::log("Успешно создана категория '$name' с id='$last_id'", $this->log_file);
            return $last_id;
        } catch (PDOException $e) {
            MyLog::log("Ошибка в заполнении категории товара ID = $this->product_id! Имя категории = $name, id = $id, parent_id = $parent_id... " . $e->getMessage(), $this->error_file);
            $this->dbh->rollback();
        }
    }

    // Добавляет цепочку категорий в базу и делает родителем цепочки категорию с интексом $id
    // $id = 3; $categories = ['cat1', 'cat2', 'cat3']
    function addCategoriesToId($id, $categories)
    {

        // получаем все категории из базы
//        6. Добавляем категории, если они не существуют
//        INSERT INTO `oc_category` (`category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES
//        (86, '', 0, 0, 1, 0, 1, '2017-10-23 16:18:37', '2017-10-23 16:18:37'),
//        (87, '', 86, 0, 1, 0, 1, '2017-10-23 16:18:46', '2017-10-23 16:18:46'),
//        (88, '', 87, 0, 1, 0, 1, '2017-10-23 16:18:54', '2017-10-23 16:18:54');

        try {
            foreach ($this->categories as $catName => $v) {
                // проверяем есть ли такая же цепочка категорий в базе
                $sql = "SELECT category_id FROM oc_category_description WHERE name LIKE ?";
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute(array($catName));
                $catId = $stmt->fetchColumn();
                if ($catId) {
                    echo "<h3> CatId='$catId' </h3>";
                } else {
                    echo "<h3> Надо создать категорию '$catName' </h3>";
                    $this->categories[$catName] = $catId;
                }
            }
            exit;
            $sql = "INSERT INTO oc_category (category_id, image, parent_id, top, column, sort_order, status, date_added, date_modified)";
            $sql .= " VALUES (:category_id, '', :parent_id, 0, 1, 0, 1, now(), now())";
            $stmt = $this->dbh->prepare($sql);

            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':image', $image);
            foreach ($this->images as $t) {
                $image = $this->images_prefix . $t;
                $stmt->execute();
            }
            MyLog::log("Успешно добавлены КАРТИНКИ товара №$this->product_id !", $this->log_file);
        } catch (PDOException $ex) {
            MyLog::log("Не удалось создать картинки товара №$this->product_id! " . $ex->getMessage(), $this->error_file);
        }

        return $this;
    }

    // создаем категории, добавляем описания, регистрируем категории в магазине, если надо добавляем алиасы к категориям в сео-про (можно и руками, категорий не много)
    function addCategoriesToDB()
    {
        echo "addCategoryToDB();<br>";

        if (count($this->categories) > 0) {
            $this->getAllCategoryFromDB();
            $answer = $this->all_categories->createOrUpdateCategory($this->categories);
            $this->addCategoriesToId($answer['id'], $answer['categories']);
        }

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
        // Картинки
        if (isset($_POST[images])) {
            // images = 'img1|img2|img3'
            try {
                $this->images = explode('|', $_POST[images]);
            } catch (Exception $ex) {
                MyLog::log("В товаре №$this->product_id КАРТИНКИ заданы с ошибками! POST_[images]=$_POST[images]", $this->error_file);
            }
        }
        // Категории
        if (isset($_POST[categories])) {
            try {
                $tt = explode('|', $_POST[categories]);
                foreach ($tt as $t) {
                    $this->categories[] = $t;
                }
            } catch (Exception $ex) {
                MyLog::log("В товаре №$this->product_id КАТЕГОРИИ заданы с ошибками! POST_[categories]=$_POST[categories]", $this->error_file);
            }
        }

        return $this;
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

    function getAllCategoryFromDB()
    {
        try {
            $sql = "SELECT oc_category_description.name as name, oc_category.category_id as id, oc_category.parent_id as parent_id";
            $sql .= " FROM oc_category, oc_category_description";
            $sql .= " WHERE oc_category.category_id = oc_category_description.category_id";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if (count($result) == 0) {
                MyLog::log("В базе категорий пока нет!", $this->log_file);
            } elseif (count($result) > 0) {
                MyLog::log("Успешно получили из базы все категории", $this->log_file);
                $tt = [];
                foreach ($result as $t) {
                    $tt[] = new Category($t['name'], $t['id'], $t['parent_id']);
                }
                $this->all_categories = new Categories($tt);
            } else {
                throw new MyException("Ошибка в логике при запросе категорий из базы! ");
            }
        } catch (Exception $ex) {
            MyLog::log("Ошибка! Не удалось получить из базы все категории", $this->error_file);
        }

        return $this;
    }

    function mainPotok()
    {
        echo "mainPotok();<br>";
        //$this->checkParams()->addProductToDB()->addDescriptionToDB()->addImagesToDB()->addLayoutToDB()->addMagazineToDB()->addCategoryToDB()->setCategoriesToDB()->addAttributesGroupToDB()->addAttributesToDB()->setAttributesToDB();
        // $this->checkParams()->addCategoriesToDB();
        $this->checkParams()->addCategoryToDB("Вася из Урюпинска", 100, 81);
    }

    public function __invoke()
    {
        $this->mainPotok();
    }
}
