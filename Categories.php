<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Categories
 *
 * @author Konstantin Semenov
 */
require_once 'MyException.php';
require_once 'Category.php';

class Categories
{

    var $categories = [];
    var $category_lists = [];
    var $category_all_lists = [];

    function __construct($category_array)
    {
        foreach ($category_array as $t) {
            $this->categories[$t->id] = $t;
        }

        return $this;
    }

    function generateCategoryList($last_id, $tt)
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

    function generateAllCategoryList($last_id, $tt, $t)
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

    function generateCategoryLists()
    {
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

//        var_dump($this->category_lists);

        return $this;
    }

    function generateAllCategoryLists()
    {
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
    function createOrUpdateCategory($bread_crumps)
    {
        if (is_array($bread_crumps)) {
            foreach ($bread_crumps as $t) {
                if (!is_string($t)) {
                    throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
                }
            }
        } else {
            throw new MyException('Ошибка в логике! Категории товара надо передавать как массив строк!');
        }

        $this->generateAllCategoryLists();

//        M = [a, ab, abc, ad];   x = ka
//
//        1).
//        r = []
//        x = ka
//
//        2).
//        r = [a]
//        x = k
//
//        3).
//        r = [ka]
//        x = ''
//
//        4). 
//        STOP, потому, что x пустой
//        addCategory(parent_id=0, список_категорий=[ka])

        $result = [];
        $x = $bread_crumps;
        $id = 0;

        while (count($x) > 0) {
            foreach ($this->category_all_lists as $key => $tt) {
                if ($tt == $x) {
                    return $otvet = array('id' => $key,'categories' => $result);
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

    function printArray($tt, $text = '')
    {
        if (is_array($tt)) {
            echo "<br><hr><h3>$text</h3>";
            if (is_array($tt[array_rand($tt)])) {
                foreach ($tt as $t) {
                    $this->printArray($t);
                }
            } elseif (is_string($tt[array_rand($tt)])) {
                echo "[ ";
                for ($i = 0; $i < count($tt); $i++) {
                    if ($i != 0) {
                        echo " :: ";
                    }
                    echo $tt[$i];
                }
                echo " ]<hr><br>";
            } elseif (count($tt) == 0) {
                echo "[ ]<hr><br>";
            }
        }
    }
}

