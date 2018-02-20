<?php

class Manufacturer
{

    public $id; // id производителя
    public $name; // Название производителя
    public $exists_in_db; // есть ли такой производитель в базе данных
    public $to_product; // этого производителя надо назначить продукту
    public $added_to_product; // этот производитель мы уже назначили товару

    public function __construct($id, $name, $exists_in_db = 0, $to_product = 0, $added_to_product = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->exists_in_db = $exists_in_db;
        $this->to_product = $to_product;
        $this->added_to_product = $added_to_product;
    }
}
