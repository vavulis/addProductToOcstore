<?php
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Manufacturer.php';

class Manufacturers
{

    public $manufacturers = []; // массив объектов Manufacturer , где ключи массива - id производителя. Тут будут производители, которые надо добавить и которые уже в базе (благодаря Manufacturer->exists_in_db можно понять)
    private $language_id = 1; // чтобы не переписывать тесты

    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;
    }

    private function addManufacturers(array $manufacturers)
    {
        try {
            if (count($manufacturers) > 0) {
                foreach ($manufacturers as $t) {
                    if (is_object($t) && (get_class($t) == 'Manufacturer')) {
                        if (!isset($this->$manufacturers[$t->id])) {
                            $this->$manufacturers[$t->id] = $t;
                        } else {
                            throw new MyException('ПРОИЗВОДИТЕЛЬ с таким id уже есть! Manufacturers:' . serialize($t));
                        }
                    } else {
                        throw new MyException('ПРОИЗВОДИТЕЛЬ задан неправильно! Manufacturers:' . serialize($t));
                    }
                    $this->$manufacturers[$t->id] = $t;
                }
            }
        } catch (Exception $ex) {
            throw new MyException('Ошибка в addManufacturers(). Параметр $manufacturers = ' . serialize($manufacturers) . 'Текст ошибки: ' . $ex->getMessage());
        }
        return $this;
    }

    private function addManufacturer(Manufacturer $manufacturer)
    {
        if (!isset($this->manufacturers[$manufacturer->id])) {
            $this->manufacturers[$manufacturer->id] = $manufacturer;
            return $this;
        } else {
            throw new MyException('ПРОИЗВОДИТЕЛЬ с таким id уже есть! Manufacturer:' . serialize($manufacturer));
        }
    }

    // Идем в БД и забираем оттуда всех производителей
    // Результат сохраняем в $this->manufacturers - массиве из объектов Manufacturer
    private function getAllManufacturersFromDB(PDO $dbh)
    {
        try {
            $sql = 'SELECT manufacturer_id as id, name FROM oc_manufacturer';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $this->manufacturers[] = null; // очищаем предыдущие результаты
            foreach ($result as $tt) {
                $this->manufacturers[$tt['id']] = new Manufacturer($tt['id'], $tt['name'], 1);
            }
            if (count($result) == 0) {
//                MyLog::log("В базе пока нет ни одного производителя!", $this->log_file);
            } elseif (count($result) > 0) {
//                MyLog::log("Успешно получили список производителей", $this->log_file);
            } else {
                throw new MyException("Ошибка в логике в getAllManufacturersFromDB() при запросе производителей из базы! ");
            }
        } catch (PDOException $ex) {
            throw new MyException("Ошибка в mysql при попытке получить всех производителей из базы в getAllManufacturersFromDB()!");
        }

        return $this;
    }

    public function __construct(PDO $dbh)
    {
        $this->getAllManufacturersFromDB($dbh);
    }

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

    // возвращает id новосозданного производителя, а если такой уже есть - то его id
    public function createManufacturer(PDO $dbh, $manufacturer_name)
    {
        // пустых названий нам не надо
        if ($manufacturer_name === '') {
            return [
                'status' => 'error',
                'msg' => 'Не задано название производителя!'
            ];
        }

        // проверка на допустимые символы в НАЗВАНИИ ПРОИЗВОДИТЕЛЯ
        $bad_symbols = ['$', '#', ';', '!', ',', '@', '%', '~', '`', ':', '&', '<', '>', '[', ']', '/', '\\'];
        if ($this->isBadSymbols($bad_symbols, $manufacturer_name)) {
            return [
                'status' => 'error',
                'msg' => 'В имени производителя есть недопустимые символы!'
            ];
        }

        // проверка на дубли. если такой производитель уже есть, не создаем его, а возвращаем его id
        foreach ($this->manufacturers as $brand) {
            if ($brand->name === $manufacturer_name) {
                return [
                    'status' => 'ok',
                    'id_of_created_manufacturer' => $brand->id
                ];
            }
        }

        // добавляем ПРОИЗВОДИТЕЛЯ в базу
        try {
            $sql = "INSERT INTO `oc_manufacturer` (`name`, `sort_order`) VALUES (:name, 0)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $manufacturer_name, PDO::PARAM_STR);
            $stmt->execute();
            $new_manufacturer_id = $dbh->lastInsertId();
            $sql2 = "INSERT INTO `oc_manufacturer_description` (`manufacturer_id`, `language_id`, `name`, `description`, `meta_title`, `meta_h1`, `meta_description`, `meta_keyword`) VALUES (:manufacturer_id, :language_id, :name, '', '', '', '', '')";
            $stmt = $dbh->prepare($sql2);
            $stmt->bindParam(':manufacturer_id', $new_manufacturer_id, PDO::PARAM_INT);
            $stmt->bindParam(':language_id', $this->language_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $manufacturer_name, PDO::PARAM_STR);
            $stmt->execute();
            $sql3 = "INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`, `store_id`) VALUES (:manufacturer_id, 0)";
            $stmt = $dbh->prepare($sql3);
            $stmt->bindParam(':manufacturer_id', $new_manufacturer_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $ex) {
            throw new MyException('Ошибка в mysql при создании ПРОИЗВОДИТЕЛЯ товара');
        }

        // добавляем новосозданныго производителя в $this->manufacturers
        $this->addManufacturer(new Manufacturer($new_manufacturer_id, $manufacturer_name, 1));

        return [
            'status' => 'ok',
            'id_of_created_manufacturer' => $new_manufacturer_id
        ];
    }

    // назначаем товару ПРОИЗВОДИТЕЛЯ
    private function setManufacturerToProduct(PDO $dbh, $product_id, Manufacturer $manufacturer)
    {
        $this->manufacturers[$manufacturer->id]->to_product = 1;
        try {
            $sql = "UPDATE oc_product SET manufacturer_id=:manufacturer_id, date_modified=now() WHERE product_id = :product_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':manufacturer_id', $manufacturer->id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $ex) {
            throw new MyException('Ошибка в mysql при назначении товару ПРОИЗВОДИТЕЛЯ в setManufacturerToProduct()!');
        }
        $this->manufacturers[$manufacturer->id]->to_product = 0;
        $this->manufacturers[$manufacturer->id]->added_to_product = 1;
    }

    // создает производителя и назначает его товару
    public function setUpManufacturer(PDO $dbh, $product_id, $manufacturer_name)
    {
        $result = $this->createManufacturer($dbh, $manufacturer_name);
        switch ($result['status']) {
            case 'ok':
                $this->setManufacturerToProduct($dbh, $product_id, $this->manufacturers[$result['id_of_created_manufacturer']]);
                break;
            case 'error':
                throw new MyException($result['msg']);
            default :
                throw new MyException('Ошибка в логике в setUpManufacturer()!');
        }
    }
}
