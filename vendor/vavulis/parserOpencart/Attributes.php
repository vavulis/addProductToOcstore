<?php
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Attribute.php';

class Attributes
{

    public $attributes = []; // Массив объектов Attribute, где ключи массива - id атбирутов. Тут будут атрибуты, которые надо бодавить и которые уже в базе (благодаря Attribute->exists_in_db можно понять)

    public function addAttributes(array $attributes)
    {
        try {
            if (count($attributes) > 0) {
                foreach ($attributes as $t) {
                    if (is_object($t) && (get_class($t) == 'Attribute')) {
                        if (!isset($this->attributes[$t->id])) {
                            $this->attributes[$t->id] = $t;
                        } else {
                            throw new MyException('АТРИБУТ с таким id уже есть! Attributes:' . serialize($t));
                        }
                    } else {
                        throw new MyException('АТРИБУТ задан неправильно! Attributes:' . serialize($t));
                    }
                    $this->attributes[$t->id] = $t;
                }
            }
        } catch (Exception $ex) {
            throw new MyException('Ошибка в addAttributes(). Параметр $attributes = ' . serialize($attributes) . 'Текст ошибки: ' . $ex->getMessage());
        }
        return $this;
    }

    // Смотрим в базе, какие атрибуты уже назначены товару и делаем соответствующие пометки в $this->attributes->old_value, чтобы потом не дублировать атрибуты, а обновлять их
    function updateAttributes(PDO $dbh, $product_id)
    {
        try {
            $sql = "SELECT attribute_id, text FROM oc_product_attribute WHERE product_id = :product_id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            $oc_product_attribute = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // помечаем уже назначенные товару атрибуты у себя в $this->attributes
            foreach ($oc_product_attribute as $tt) {
                $this->attributes[$tt['attribute_id']]->old_val = $tt['text'];
            }
            // надо добавить это событие в ЛОГИ
        } catch (PDOException $ex) {
            throw new MyException('Ошибка в mysql в updateAttributes()!');
        }
    }

    function __construct(PDO $dbh, $product_id, array $attributes = [])
    {
        if ($dbh == null) {
            throw new MyException('В конструктор Attributes передан дескриптор базы данных, равный нулю');
        }
        $this->getAllAttributesFromDB($dbh);
        $this->addAttributes($attributes);
        $this->updateAttributes($dbh, $product_id);

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getIdByName($group_name, $name)
    {
        foreach ($this->attributes as $k => $v) {
            if (($v->group_name == $group_name) && ($v->name == $name)) {
                return $k;
            }
        }
        return -1;
    }

    public function addAttribute(Attribute $attribute)
    {
        if (!isset($this->attributes[$attribute->id])) {
            $this->attributes[$attribute->id] = $attribute;
            return $this;
        } else {
            throw new MyException('АТРИБУТ с таким id уже есть! Attribute:' . serialize($attribute));
        }
    }

    // Идем в БД и забираем оттуда все категории и группы категорий
    // Результат сохраняем в $this->allAttributes - массиве из объектов Attribute
    public function getAllAttributesFromDB(PDO $dbh)
    {
        try {
            $sql = 'SELECT oc_attribute_group_description.name as group_name, oc_attribute_group.attribute_group_id as group_id, oc_attribute_description.name as name, oc_attribute.attribute_id as id';
            $sql .= ' FROM oc_attribute, oc_attribute_description, oc_attribute_group, oc_attribute_group_description';
            $sql .= ' WHERE oc_attribute_group.attribute_group_id = oc_attribute_group_description.attribute_group_id';
            $sql .= ' AND oc_attribute_group.attribute_group_id = oc_attribute.attribute_group_id';
            $sql .= ' AND oc_attribute.attribute_id = oc_attribute_description.attribute_id';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $tt) {
                $this->attributes[$tt['id']] = new Attribute($tt['group_name'], $tt['group_id'], $tt['name'], $tt['id'], 1, 0, 0);
            }
            if (count($result) == 0) {
//                MyLog::log("В базе категорий пока нет!", $this->log_file);
            } elseif (count($result) > 0) {
//                MyLog::log("Успешно получили из базы все категории", $this->log_file);
            } else {
                throw new MyException("Ошибка в логике в getAllCategoryFromDB() при запросе категорий из базы! ");
            }
        } catch (PDOException $ex) {
            throw new MyException("Ошибка при попытке получить все атрибуты из базы в getAllAttributesFromDB()!");
        }

        return $this;
    }

    // 11. Добавляем группу атрибутов
    // INSERT INTO `oc_attribute_group` (`sort_order`) VALUES
    // (0);INSERT INTO `oc_attribute_group_description` (`attribute_group_id`, `language_id`, `name`) VALUES
    // (LAST_INSERT_ID(), 1, 'Информация о товаре');
    public function createGroupOfAttributes(PDO $dbh, $group_name)
    {
        try {
            $sql = "INSERT INTO `oc_attribute_group` (`sort_order`) VALUES (0)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $new_group_id = $dbh->lastInsertId();

            $sql = "INSERT INTO `oc_attribute_group_description` (`attribute_group_id`, `language_id`, `name`) VALUES (:group_id, 1, :group_name)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':group_id', $new_group_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
            $stmt->execute();
            // надо добавить это событие в ЛОГИ
        } catch (PDOException $ex) {
            throw new MyException('Ошибка в mysql при создании ГРУППЫ_АТРИБУТОВ! ');
        }

        return $new_group_id;
    }

    // 12. Добавляем атрибут в группу. Номер группы=4
    // INSERT INTO `oc_attribute` (`attribute_group_id`, `sort_order`) VALUES
    // (4, 0);INSERT INTO `oc_attribute_description` (`attribute_id`, `language_id`, `name`) VALUES
    // (LAST_INSERT_ID(), 1, 'Год издания');
    public function createAttribute(PDO $dbh, $group_id, $name)
    {
        try {
            $sql = "INSERT INTO `oc_attribute` (`attribute_group_id`, `sort_order`) VALUES (:group_id, 0)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->execute();
            $new_attribute_id = $dbh->lastInsertId();

            $sql = "INSERT INTO `oc_attribute_description` (`attribute_id`, `language_id`, `name`) VALUES (:new_attribute_id, 1, :name)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':new_attribute_id', $new_attribute_id, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            // надо добавить это событие в ЛОГИ
        } catch (PDOException $ex) {
            throw new MyException('Ошибка в mysql при создании АТРУБИТА!');
        }

        return $new_attribute_id;
    }

    // 13. Добавляем атрубит к товару и задаем иму значение
    // INSERT INTO `oc_product_attribute` (`product_id`, `attribute_id`, `language_id`, `text`) VALUES
    // (1, 6, 1, '1975');
    public function setAttributeToProduct(PDO $dbh, $product_id, Attribute $attribute)
    {
        // если атрибут уже назначен товару, надо его обновить
        if (($attribute->old_val) || ($attribute->old_val === '')) {
            if ((!$attribute->val) || ($attribute->val === '')) {
                throw new MyException("Ошибка в setAttributeToProduct()! Задано пустое значение атрибута!");
            }
            try {
                $sql = "UPDATE oc_product_attribute SET text = :new_val ";
                $sql .= "WHERE oc_product_attribute.product_id = :product_id ";
                $sql .= "AND oc_product_attribute.attribute_id = :attribute_id ";
                $sql .= "AND oc_product_attribute.language_id = 1";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':new_val', $attribute->val, PDO::PARAM_STR);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':attribute_id', $attribute->id, PDO::PARAM_INT);
                $stmt->execute();
                // надо добавить это событие в ЛОГИ
            } catch (PDOException $ex) {
                throw new MyException('Ошибка в mysql при изменении АТРИБУТА ПРОДУКТА!');
            }
        } else {
            if ($attribute->val) {
                try {
                    $sql = "INSERT INTO `oc_product_attribute` (`product_id`, `attribute_id`, `language_id`, `text`) VALUES (:product_id, :attribute_id, 1, :text)";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmt->bindParam(':attribute_id', $attribute->id, PDO::PARAM_INT);
                    $stmt->bindParam(':text', $attribute->val, PDO::PARAM_STR);
                    $stmt->execute();
                    // надо добавить это событие в ЛОГИ
                } catch (PDOException $ex) {
                    throw new MyException('Ошибка в mysql при назначении АТРИБУТА ПРОДУКТА!');
                }
            } elseif ((!$attribute->val) || ($attribute->val === '')) {
                throw new MyException("Ошибка в setAttributeToProduct()! Задано пустое значение атрибута!");
            }
        }
    }

    // ищет такой же атрибут в $this->attributes, если на находит - ищет такую же группу. возвращает найденные id группы_атрибутов и id атрибута
    public function findSimilarAttribute($group_name, $name)
    {
        // проверка на ошибки в параметрах
        if (!$name) {
            return [
                'status' => 'error',
                'data' => [
                    'msg' => 'не заполнено имя атрибута!'
                ]
            ];
        }
        if (!$group_name) {
            return [
                'status' => 'error',
                'data' => [
                    'msg' => 'не заполнена группа атрибутов!'
                ]
            ];
        }
        // найденные id-шники группы и атрибута
        $finded = [
            'similar_group_id' => null,
            'similar_name_id' => null
        ];
        // ищем похожие группы и атрибуты и записываем  найденное в $finded
        foreach ($this->attributes as $atribute) {
            if (mb_strtolower($atribute->group_name) == mb_strtolower($group_name)) {
                $finded['similar_group_id'] = $atribute->group_id;
                if (mb_strtolower($atribute->name) == mb_strtolower($name)) {
                    $finded['similar_name_id'] = $atribute->id;
                    break; // выходим из цикла - мы нашли то, что надо
                }
            }
        }
        // анализируем найденное и выдаем результат
        if ($finded['similar_group_id'] && $finded['similar_name_id']) {
            // нашли такой же атрибут
            return [
                'status' => 'findAtribute',
                'data' => [
                    'group_id' => $finded['similar_group_id'],
                    'name_id' => $finded['similar_name_id']
                ]
            ];
        } elseif ($finded['similar_group_id']) {
            // нашли такую же группу
            return [
                'status' => 'findGroup',
                'data' => [
                    'group_id' => $finded['similar_group_id']
                ]
            ];
        } elseif (!$finded['similar_group_id'] && !$finded['similar_name_id']) {
            // такого атрибута и группы не нашли
            return [
                'status' => 'findNothing',
                'data' => []
            ];
        } else {
            throw new MyException('Ошибка в логике!');
        }
    }

    // Добавляем уникальные атрибуты в базу данных и назначает их товару
    // Вычисляем УНИКАЛЬНЫЕ_АТРИБУТЫ (которые есть в POST, из которых нет в базе)
    // ДОБАВЛЯЕМ_В_БАЗУ() все УНИКАЛЬНЫЕ_АТРИБУТЫ
    // param attritubes = array(
    //        'group'=>'характеристики товара',
    //        'name'=>'цвет',
    //        'val'=>'красный'
    //    )
    public function setAttributesToProduct(PDO $dbh, $product_id, array $attributes)
    {
        foreach ($attributes as $a) {
            $finded = $this->findSimilarAttribute($a['group'], $a['name']);
            switch ($finded['status']) {
                case 'findAtribute':
                    $atr_id = $finded['data']['name_id'];
                    $this->attributes[$atr_id]->to_product = 1; // создавать атрибуты и группы уже не надо. только привязать к товару потом
                    $this->attributes[$atr_id]->val = $a['val']; // задаем значение атрибута                    
                    $this->setAttributeToProduct($dbh, $product_id, $this->attributes[$atr_id]);
                    // больше этот атрибут к товару назначать не надо. укажем это
                    $this->attributes[$atr_id]->to_product = 0;
                    $this->attributes[$atr_id]->added_to_product = 1;
                    break;
                case 'findGroup':
                    $group_id = $finded['data']['group_id'];
                    $t = new Attribute($a['group'], $group_id, $a['name'], null, 0, 1, 0, $a['val']);
                    $t->id = $this->createAttribute($dbh, $group_id, $a['name']); // в базе создан новый атрибут с заданной категорией. вернули его id
                    $t->exists_in_db = 1; // и группа_атрибута, и сам атрибут уже есть в базе
                    $this->addAttribute($t);
                    $this->setAttributeToProduct($dbh, $product_id, $this->attributes[$t->id]);
                    // больше этот атрибут к товару назначать не надо. укажем это
                    $this->attributes[$t->id]->to_product = 0;
                    $this->attributes[$t->id]->added_to_product = 1;
                    unset($t);
                    break;
                case 'findNothing':
                    $new_group_id = $this->createGroupOfAttributes($dbh, $a['group']); // в базе создана новая категория. вернули ее id
                    $new_atr_id = $this->createAttribute($dbh, $new_group_id, $a['name']); // в базе создан новый атрибут с заданной категорией. вернули его id
                    $t = new Attribute($a['group'], $new_group_id, $a['name'], $new_atr_id, 1, 1, 0, $a['val']);
                    $this->addAttribute($t);
                    $this->setAttributeToProduct($dbh, $product_id, $this->attributes[$t->id]);
                    // больше этот атрибут к товару назначать не надо. укажем это
                    $this->attributes[$t->id]->to_product = 0;
                    $this->attributes[$t->id]->added_to_product = 1;
                    unset($t);
                    break;
                case 'error':
                    $error_msg = $finded['data']['msg'];
                    throw new MyException($error_msg);
                    break;
                default :
                    throw new MyException('Ошибка в логике в setAttributesToProduct()!');
            }
        }
    }
}
