<?php

class Attribute
{

    public $id; // id атрибута
    public $group_id; // id группы атрибутов
    public $name; // имя атрибута
    public $group_name; // название группы атрибутов
    public $exists_in_db; // есть ли такой атрибут в базе данных
    public $to_product; // этот атрубит надо назначить продукту
    public $added_to_product; // этот атрибут мы уже назначили товару
    public $val; // новое значение атрибута
    public $old_val; // значение атрибута из базы, если такое есть

    function __construct($group_name, $group_id, $name, $id, $exists_in_db = 0, $to_product = 0, $added_to_product = 0, $val = '', $old_val = null)
    {
        $this->id = $id;
        $this->group_id = $group_id;
        $this->name = $name;
        $this->group_name = $group_name;
        $this->val = $val;
        $this->old_val = $old_val;
        $this->exists_in_db = $exists_in_db;
        $this->to_product = $to_product;
        $this->added_to_product = $added_to_product;
    }
}
