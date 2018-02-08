<?php
/**
 * Description of Categories
 *
 * @author Konstantin Semenov
 */
//namespace vavulis\parserOpencart\Categories;

define('__LOGFILE__', __ROOT__ . '/logs/messages.log');

require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';
require_once __ROOT__ . '/vendor/vavulis/parserOpencart/Category.php';

class Categories
{

    private $categories = []; // Массив объектов Category
    private $category_lists = []; // Массив цепочек категорий от корня до конца
    private $category_all_lists = []; // Массив предков для каждой категории

    public function __construct(array $category_array)
    {
        try {
            foreach ($category_array as $t) {
                if (is_object($t) && (get_class($t) == 'Category')) {
                    if (!isset($this->categories[$t->id])) {
                        $this->categories[$t->id] = $t;
                    } else {
                        throw new MyException('Категория с таким id уже есть! Category:' . serialize($t));
                    }
                } else {
                    throw new MyException('Категория задана неправильно! Category:' . serialize($t));
                }
                $this->categories[$t->id] = $t;
            }
            $this->generateAllCategoryLists();
            $this->generateCategoryLists();
        } catch (Exception $ex) {
            throw new MyException('Ошибка в конструкторе Categories. Параментр category_array = ' . serialize($category_array) . 'Текст ошибки: ' . $ex->getMessage());
        }
        return $this;
    }

    public function addCategory(Category $category)
    {
        if (!isset($this->categories[$category->id])) {
            $this->categories[$category->id] = $category;
            $this->generateAllCategoryLists();
            $this->generateCategoryLists();
            return $this;
        } else {
            throw new MyException('Категория с таким id уже есть! Category:' . serialize($category));
        }
    }

    private function generateCategoryList($last_id, $tt)
    {
        if (isset($this->categories[$last_id])) {
            if ($this->categories[$last_id]->parent_id == 0) {
                $tt[] = $this->categories[$last_id]->name;
                $this->category_lists[] = array_reverse($tt);
            } else {
                $tt[] = $this->categories[$last_id]->name;
                $this->generateCategoryList($this->categories[$last_id]->parent_id, $tt);
            }
        } else {
            throw new MyException("Ошибка в логике. parent_id ссылается на несуществующий элемент. LAST_ID = $last_id, TT = $tt. ");
        }
    }

    private function generateAllCategoryList($last_id, $tt, $t)
    {
        if (isset($this->categories[$last_id])) {
            if ($this->categories[$last_id]->parent_id == 0) {
                $tt[] = $this->categories[$last_id]->name;
                $this->category_all_lists[$t] = array_reverse($tt);
            } else {
                $tt[] = $this->categories[$last_id]->name;
                $this->generateAllCategoryList($this->categories[$last_id]->parent_id, $tt, $t);
            }
        } else {
            throw new MyException("Ошибка в логике. parent_id ссылается на несуществующий элемент. LAST_ID = $last_id, TT = $tt. ");
        }
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function generateCategoryLists()
    {
        // сначала удалим старые значения
        $this->category_lists = [];
        // 1. найдем крайние элементы цепочки
        // на крайнее элементы не ссылаются другие елементы
        $parents_cat = []; // категории, которые уже родили
        $ids_cat = []; // айдишники категорий
        foreach ($this->categories as $c) {
            $parents_cat[] = $c->parent_id;
            $ids_cat[] = $c->id;
        }
        $parents_cat = array_unique($parents_cat);
        $ids_cat = array_unique($ids_cat);
        sort($parents_cat, SORT_NUMERIC);
        sort($ids_cat, SORT_NUMERIC);
        $last_ids = array_diff($ids_cat, $parents_cat);
        // 2. построем цепочки от крайних элементов
        foreach ($last_ids as $last_id) {
            $this->generateCategoryList($last_id, []);
        }
        return $this;
    }

    // для каждой категории находит список предков. сохраняем все цепочки в массив $this->category_all_list
    public function generateAllCategoryLists()
    {
        // сначала удалим старые данные
        $this->category_all_lists = [];
        // а теперь наполняем новымы значениями свойство класса $this->category_all_lists
        foreach ($this->categories as $c) {
            $this->generateAllCategoryList($c->id, [], $c->id);
        }
        // убедимся, что мы сгенерировали массив массивов строк
        if (is_array($this->categories) && count($this->categories) > 0) {
            if (is_array($this->category_all_lists)) {
                foreach ($this->category_all_lists as $tt) {
                    if (is_array($tt)) {
                        foreach ($tt as $t) {
                            if (is_string($t)) {
                                return $this;
                            } else {
                                throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
                            }
                        }
                    } else {
                        throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
                    }
                }
            } else {
                throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
            }
        }
        return $this;
    }

    // $bread_crumps = ['Книги', 'Русские', 'Научные']
    // return = ['id' => 30, 'categories' => ['Духовные', 'Жития святых']]
    // or return = NULL если категорию создавать не надо
    public function createOrUpdateCategory($bread_crumps)
    {
        if (is_array($bread_crumps)) {
            foreach ($bread_crumps as $key => $t) {
                if (!is_string($t)) {
                    throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
                } elseif ($t == '') {
                    return NULL; // категорию добавлять не надо
                } else {
                    $bread_crumps[$key] = trim($t); // удаляем пробелы слева и справа
                }
            }
            unset($key);
            unset($t);
        } else {
            throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
        }
        if (count($this->categories) == 0) {
            return array('id' => 0, 'categories' => $bread_crumps);
        }
        $result = [];
        $x = $bread_crumps;
        $id = 0;
        while (count($x) > 0) {
            foreach ($this->category_all_lists as $key => $tt) {
                if ($tt == $x) {
                    if (count($result) == 0) {
                        return NULL; // такая категория существует, категорию добавлять не надо
                    } else {
                        return array('id' => $key, 'categories' => $result);
                    }
                }
            }
            array_unshift($result, array_pop($x));
        }
        if (count($x) == 0) {
            return array('id' => $id, 'categories' => $result);
        } else {
            throw new MyException('Ошибка в логике!');
        }
        throw new MyException('Ошибка в логике!');
    }

    private function addArray($a, $b)
    {
        foreach ($b as $t) {
            $a[] = $t;
        }
        return $a;
    }

    // $id = 3 - id категории
    // return = [0, 1, 2] - массив из id категорий предков
    public function getParentsChain($id)
    {
        if (isset($this->categories[$id])) {
            if ($this->categories[$id]->parent_id == 0) {
                return [$id];
            } else {
                return $this->addArray([$id], $this->getParentsChain($this->categories[$id]->parent_id));
            }
        } else {
            throw new MyException("Ошибка в логике. parent_id ссылается на несуществующий элемент. LAST_ID = $id");
        }
    }

//    На входе: $names = ['cat1', 'cat2', 'cat3'] - цепочка категорий
//    На выходе: $result = [0, 59, 60] - айдишники категорий
    public function getIdsByNames(array $names)
    {
        foreach ($this->category_all_lists as $k => $v) {
            if ($names == $v) {
                $result = $this->getParentsChain($k);
                return array_reverse($result);
            }
        }
        return [];
    }
}
