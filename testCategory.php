<?php
header('Content-Type: text/html; charset=utf-8');
require 'Category.php';

class Product
{

    var $oc_cat = [];
    var $result = [];

    function __construct()
    {
        $this->oc_cat[] = new Category('книги', 1, 0);
        $this->oc_cat[] = new Category('зарубежные', 2, 1);
        $this->oc_cat[] = new Category('художественные', 3, 2);
        $this->oc_cat[] = new Category('научные', 4, 3);
        $this->oc_cat[] = new Category('русские', 5, 1);
        $this->oc_cat[] = new Category('художественные', 6, 5);
        $this->oc_cat[] = new Category('научные', 7, 5);
    }

    function findCatByName($name)
    {
        foreach ($this->oc_cat as $t) {
            if ($name == $t->name) {
                return 1;
            }
        }
        return 0;
    }

    function addCat($post)
    {
        if (count($post) == 1) {
            echo "<p>длина массива стала равна ОДИН</p>";
            return 0;
        } else {
            echo "<p>длина массива равна" . count($post) . "</p>";
            $last = array_pop($post);
            if ($this->findCatByName($last)) {
                echo "<p>Категорию<b> $last</b> существует.</p>";
                return $this->addCat($post);
            } else {
                echo "<p>Категории<b> $last</b> не существует. Ничего не делаем.</p>";
                $this->result[] = array('name' => $last, 'parent_id' => $this->addCat($post));
                return $this->addCat($post);
            }
        }
    }
}

$product = new Product();
$product->addCat(['книги', 'русские', 'религиозные']);
var_dump($product->result);





