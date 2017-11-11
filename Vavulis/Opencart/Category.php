<?php
/**
 * Description of Category
 *
 * @author Konstantin Semenov
 */
class Category
{

    var $id = 0;
    var $name = '';
    var $parent_id = 0;
    
    function __construct($name, $id, $parent_id = 0)
    {
        $this->name = trim($name);
        $this->id = $id;
        $this->parent_id = $parent_id;
        return $this;
    }

}


