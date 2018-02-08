<?php
//namespace vavulis\parserOpencart;
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/logs/MyLog.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Categories.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Attributes.php';

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
    var $all_categories = NULL; // type = Categories
    // Массив категорий, переданных через $_POST
    var $categories_from_post = [];
    // alias для категории для seopro
    var $category_alias_seopro = '';
    // alias для товара для seopro
    var $product_alias_seopro = '';
    // Группа атрибутов, имя атрибута, значение атрибута.
    //    attritubes = array(
    //        'group'=>'характеристики товара',
    //        'name'=>'цвет',
    //        'val'=>'красный'
    //    )
    var $attributes = [];
    // Файл логов
    var $log_file = __ROOT__ . '/logs/messages.log';
    // Файл логов ошибок
    var $error_file = __ROOT__ . '/logs/error.log';
    // Новосозданные категории Categories
    var $newCategories = []; // type = arra of Category
    // Назначенный товару категории
    var $categories = []; // type = Categories

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

    function addProductToDB()
    {
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

    private function addCategoryToDB($name, $parent_id)
    {
        try {
            $this->dbh->beginTransaction();
            $sql = "INSERT INTO `oc_category` (`category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`)";
            $sql .= " VALUES ('', '', :parent_id, 0, 1, 0, 1, now(), now())";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $stmt->execute();

            $last_id = $this->dbh->lastInsertId();

            $sql2 = "INSERT INTO `oc_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_h1`, `meta_description`, `meta_keyword`)";
            $sql2 .= " VALUES (:category_id, 1, :name, '', '', '', '', '')";
            $stmt2 = $this->dbh->prepare($sql2);
            $stmt2->bindParam(':category_id', $last_id, PDO::PARAM_INT);
            $stmt2->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt2->execute();

            $sql3 = "INSERT INTO `oc_category_to_store` (`category_id`, `store_id`) VALUES (:category_id, 0)";
            $stmt3 = $this->dbh->prepare($sql3);
            $stmt3->bindParam(':category_id', $last_id, PDO::PARAM_INT);
            $stmt3->execute();

            $sql4 = "INSERT INTO `oc_category_to_layout` (`category_id`, `store_id`, `layout_id`) VALUES (:category_id, 0, 0)";
            $stmt4 = $this->dbh->prepare($sql4);
            $stmt4->bindParam(':category_id', $last_id, PDO::PARAM_INT);
            $stmt4->execute();

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
    private function addCategoriesToId($id, $categories)
    {
        if (count($categories) == 0) {
            return $id;
        } elseif (count($categories) > 0) {
            $cat = array_shift($categories);
            $t = $this->addCategoryToDB($cat, $id);
            $this->newCategories[] = new Category($cat, $t, $id);
            $this->addCategoriesToId($t, $categories);
        } else {
            throw new MyException("Ошибка в логике при создании цепочки категорий.");
        }

        return -1;
    }

    // создаем категории, добавляем описания, регистрируем категории в магазине, если надо добавляем алиасы к категориям в сео-про (можно и руками, категорий не много)    
    public function addCategoriesToDB()
    {
        if (count($this->categories_from_post) > 0) {
            $this->getAllCategoryFromDB();
            $cnt = count($this->all_categories->getCategories());
            if ($cnt > 0) {
                $answer = $this->all_categories->createOrUpdateCategory($this->categories_from_post);
                $this->addCategoriesToId($answer['id'], $answer['categories']);
                // добавляем новосозданные категории в $this->new_categories
                foreach ($this->newCategories as $ncat) {
                    $this->all_categories->addCategory($ncat);
                }
            } elseif ($cnt == 0) {
                MyLog::log("Надо создать категории с нуля", $this->log_file);
                $this->addCategoriesToId(0, $this->categories_from_post);
                // добавляем новосозданные категории в $this->new_categories
                foreach ($this->newCategories as $ncat) {
                    $this->all_categories->addCategory($ncat);
                }
            } else {
                throw new MyException("Ошибка в логике в addCategoriesToDB()!");
            }
        }

        return $this;
    }

    // Добавляет нужную инфу в таблицу oc_category_path
    // без этого не будет работать цепочка категорий в админке если не нажать кнопку ПОЧИНИТЬ
    private function addCategoryPath()
    {
        if (count($this->newCategories) > 0) {
            $result = [];
            $newcats = [];
            $first_id = $this->newCategories[0]->parent_id;
            if (($first_id < 0) || !isset($first_id)) {
                throw new MyException('Ошибка в логике в addCategoryPath()!');
            }
            foreach ($this->newCategories as $cat) {
                $newcats[] = $cat->id;
            }
            if ($first_id > 0) {
                $head = $this->all_categories->getParentsChain($first_id);
                for ($i = count($head) - 1; $i >= 0; $i--) {
                    $result[] = $head[$i];
                }
                for ($i = 0; $i < count($newcats); $i++) {
                    $result[] = $newcats[$i];
                }
            } elseif ($first_id == 0) {
                $result = $newcats;
            } else {
                throw new MyException('Ошибка в логике в addCategoryPath()!');
            }
            $path_array = $this->generateCategoryPath($result);
            try {
                $sql = "INSERT INTO `oc_category_path` (`category_id`, `path_id`, `level`) VALUES (?, ?, ?)";
                $stmt = $this->dbh->prepare($sql);
                $category_path_from_db = $this->getCategoryPathFromDb();
                foreach ($path_array as $path) {
                    foreach ($category_path_from_db as $path_db) {
                        if (($path_db['category_id'] == $path[0]) && ($path_db['path_id'] == $path[1])) {
                            echo "<br>НЕ НАДО ВСТАВЛЯТЬ path<br>";
                            echo $path_db['category_id'] . '=' . $path[0];
                            echo "<br>";
                            echo $path_db['path_id'] . '=' . $path[1];
                            echo "<br>";
                        } else {
                            echo "<br>НАДО ВСТАВИТЬ path<br>";
                            echo $path_db['category_id'] . '=' . $path[0];
                            echo "<br>";
                            echo $path_db['path_id'] . '=' . $path[1];
                            echo "<br>";
                            $stmt->execute($path);
                        }
                    }
                }
            } catch (PDOException $ex) {
                throw new MyException($path_db['category_id'] . '::' . $path_db['path_id']);
            }
        } else {
            
        }

        return $this;
    }

    // вспомогательная ф-ция для ф-ции addCategoryPath()
    // param $cats = array of category.id; $cats = [56, 57, 58]
    // return = массив строк, который надо добавить в таблицу oc_category_path
    // return = [ [56 56 0], [57 57 1], [57 56 0] ]
    private function generateCategoryPath($cats)
    {
        $result = [];
        for ($i = 0; $i < count($cats); $i++) {
            for ($j = 0; $j < count($cats); $j++) {
                if ($i >= $j) {
                    $result[] = [$cats[$i], $cats[$j], $j];
                }
            }
        }
        return $result;
    }

    private function getCategoryPathFromDb()
    {
        try {
            $sql = "SELECT * FROM `oc_category_path`";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                MyLog::log("В базе oc_category_path пусто!", $this->log_file);
                return [];
            } elseif (count($result) > 0) {
                MyLog::log('Успешно получили из базы записи из таблицы oc_category_path! ' . serialize($result), $this->log_file);
                return $result;
            } else {
                throw new MyException("Ошибка в логике в getCategoryPathFromDb()! ");
            }
        } catch (PDOException $ex) {
            throw new MyException("Ошибка в MYSQL в getCategoryPathFromDb()!");
        }
    }

    // назначаем товару категории
    public function setCategoriesToDB()
    {
        if ($this->product_id == '') {
            throw new MyException("Ошибка! Невозможно назначить категорию, так как не задан product_id. product_id = '$this->product_id'! ");
        }

        if (count($this->categories_from_post) > 0) {
            try {
                $cnt = count($this->all_categories->getCategories());
                if ($cnt > 0) {
                    $ids = $this->all_categories->getIdsByNames($this->categories_from_post);
                    // назначаем товару категории из $ids
                    for ($i = count($ids) - 1; $i >= 0; $i--) {
                        if ($i == count($ids) - 1) {
                            $sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`, `main_category`) VALUES (:product_id, :last_id, 1)";
                        } else {
                            $sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`, `main_category`) VALUES (:product_id, :last_id, 0)";
                        }
                        $stmt = $this->dbh->prepare($sql);
                        $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
                        $stmt->bindParam(':last_id', intval($ids[$i]), PDO::PARAM_INT);
                        $stmt->execute();
                        MyLog::log("Успешно назначена категория №$ids[$i] товару №$this->product_id!", $this->log_file);
                    }
                } elseif ($cnt == 0) {
                    if (count($this->newCategories) > 0) {
                        for ($i = count($this->newCategories) - 1; $i >= 0; $i--) {
                            if ($i == count($this->newCategories) - 1) {
                                $sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`, `main_category`) VALUES (:product_id, :last_id, 1)";
                            } else {
                                $sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`, `main_category`) VALUES (:product_id, :last_id, 0)";
                            }
                            $stmt = $this->dbh->prepare($sql);
                            $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
                            $stmt->bindParam(':last_id', intval($this->newCategories[$i]->id), PDO::PARAM_INT);
                            $stmt->execute();
                            MyLog::log("Успешно назначена категория №$this->newCategories[$i]->id товару №$this->product_id!", $this->log_file);
                        }
                    } else {
                        throw new MyException('Ошибка в логике в setCategoriesToDB()!');
                    }
                } else {
                    throw new MyException('Ошибка в логике в setCategoriesToDB()!');
                }
            } catch (PDOException $ex) {
                throw new MyException("Ошибка при назначении категория №$this->newCategories[$i]->id товару №$this->product_id! $ex->getMessage()");
            }
        } else {
            MyLog::log("Нет категорий, которые надо добавить к товару", $this->log_file);
        }

        return $this;
    }

    function setAttributesToDB()
    {
        $attributes = new Attributes($this->dbh, $this->product_id);
        $attributes->setAttributesToProduct($this->dbh, $this->product_id, $this->attributes);
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
        if (isset($_POST['model'])) {
            $this->model = $_POST['model'];
        }
        if (isset($_POST['sku'])) {
            $this->sku = $_POST['sku'];
        }
        if (isset($_POST['upc'])) {
            $this->upc = $_POST['upc'];
        }
        if (isset($_POST['ean'])) {
            $this->ean = $_POST['ean'];
        }
        if (isset($_POST['jan'])) {
            $this->jan = $_POST['jan'];
        }
        if (isset($_POST['isbn'])) {
            $this->isbn = $_POST['isbn'];
        }
        if (isset($_POST['mpn'])) {
            $this->mpn = $_POST['mpn'];
        }
        if (isset($_POST['location'])) {
            $this->location = $_POST['location'];
        }
        if (isset($_POST['quantity'])) {
            $this->quantity = $_POST['quantity'];
        }
        if (isset($_POST['stock_status_id'])) {
            $this->stock_status_id = $_POST['stock_status_id'];
        }
        if (isset($_POST['image'])) {
            $this->image = $_POST['image'];
        }
        if (isset($_POST['manufacturer_id'])) {
            $this->manufacturer_id = $_POST['manufacturer_id'];
        }
        if (isset($_POST['shipping'])) {
            $this->shipping = $_POST['shipping'];
        }
        if (isset($_POST['price'])) {
            $this->price = $_POST['price'];
        }
        if (isset($_POST['points'])) {
            $this->points = $_POST['points'];
        }
        if (isset($_POST['tax_class_id'])) {
            $this->tax_class_id = $_POST['tax_class_id'];
        }
        if (isset($_POST['date_available'])) {
            $this->date_available = $_POST['date_available'];
        }
        if (isset($_POST['weight'])) {
            $this->weight = $_POST['weight'];
        }
        if (isset($_POST['weight_class_id'])) {
            $this->weight_class_id = $_POST['weight_class_id'];
        }
        if (isset($_POST['width'])) {
            $this->width = $_POST['width'];
        }
        if (isset($_POST['height'])) {
            $this->height = $_POST['height'];
        }
        if (isset($_POST['length_class_id'])) {
            $this->length_class_id = $_POST['length_class_id'];
        }
        if (isset($_POST['subtract'])) {
            $this->subtract = $_POST['subtract'];
        }
        if (isset($_POST['minimum'])) {
            $this->minimum = $_POST['minimum'];
        }
        if (isset($_POST['sort_order'])) {
            $this->sort_order = $_POST['sort_order'];
        }
        if (isset($_POST['status'])) {
            $this->status = $_POST['status'];
        }
        if (isset($_POST['viewed'])) {
            $this->viewed = $_POST['viewed'];
        }
        if (isset($_POST['date_added'])) {
            $this->date_added = $_POST['date_added'];
        }
        if (isset($_POST['date_modified'])) {
            $this->date_modified = $_POST['date_modified'];
        }
        // Описание товара
        $this->descriptions[$this->language_id] = array(
            "name" => $_POST['name'],
            "description" => $_POST['description'],
            "tag" => '',
            "meta_title" => '',
            "meta_h1" => '',
            "meta_description" => '',
            "meta_keyword" => ''
        );
        // Картинки
        if (isset($_POST['images'])) {
            // images = 'img1|img2|img3'
            try {
                $this->images = explode('|', $_POST['images']);
            } catch (Exception $ex) {
                MyLog::log("В товаре №$this->product_id КАРТИНКИ заданы с ошибками! POST_[images]=$_POST[images]", $this->error_file);
            }
        }
        // Категории
        if (isset($_POST['categories'])) {
            try {
                $tt = explode('|', $_POST['categories']);
                foreach ($tt as $t) {
                    $this->categories_from_post[] = $t;
                }
            } catch (Exception $ex) {
                MyLog::log("В товаре №$this->product_id КАТЕГОРИИ заданы с ошибками! POST_[categories]=$_POST[categories]", $this->error_file);
            }
        }

        // Атрибуты
        // $_POST['attributes'] = "Характеристики:Артикул:13497|Характеристики:Код товара:94017|Характеристики:Дата поступления:29.09.2017|Характеристики:Издательство: Правило веры, Москва"
        if (isset($_POST['attributes'])) {
            try {
                $tt = explode("|", $_POST['attributes']);
                foreach ($tt as $attribute) {
                    $t = explode(":", $attribute);
                    // если есть тройка группа_атрибута:имя_атрибута:значение_атрибута, то добавляем в атрибут
                    if (count($t) == 3) {
                        $this->attributes[] = array(
                            'group' => trim($t[0]),
                            'name' => trim($t[1]),
                            'val' => trim($t[2])
                        );
                    } else {
                        throw new MyException('Неверный формат передачи атрибутов товара: ' . serialize($attribute));
                    }
                }
            } catch (Exception $ex) {
                throw new MyException("В товаре №$this->product_id АТРИБУТЫ заданы с ошибками!" . serialize($_POST[attributes]));
            }
        }

        return $this;
    }

    function checkParams()
    {
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

    private function getAllCategoryFromDB()
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
                $this->all_categories = new Categories([]);
            } elseif (count($result) > 0) {
                MyLog::log("Успешно получили из базы все категории", $this->log_file);
                $tt = [];
                foreach ($result as $t) {
                    $tt[] = new Category($t['name'], $t['id'], $t['parent_id']);
                }
                $this->all_categories = new Categories($tt);
            } else {
                throw new MyException("Ошибка в логике в getAllCategoryFromDB() при запросе категорий из базы! ");
            }
        } catch (PDOException $ex) {
            throw new MyException("Ошибка при попытке получить все категории из базы в getAllCategoryFromDB()!");
        }

        return $this;
    }

    function mainPotok()
    {
        $this->checkParams()->addProductToDB()->addDescriptionToDB()->addImagesToDB()->addLayoutToDB()->addMagazineToDB();
        $this->addCategoriesToDB()->setCategoriesToDB()->setAttributesToDB();
    }

    public function __invoke()
    {
        $this->mainPotok();
    }
}
