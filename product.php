<?php
/**
 * Класс ТОВАР, отображение таблицы oc_product в ocStore
 *
 * @author Konstantin Semenov
 */
class Product {
    var $product_id; // ''
    var $model; // '7938'
    var $sku; // ''
    var $upc; // ''
    var $ean; // ''
    var $jan; // ''
    var $isbn; // '978-5-94759-201-6'
    var $mpn; // ''
    var $location; // ''
    var $quantity; // 6
    var $stock_status_id; // 7
    var $image; // 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg'
    var $manufacturer_id; // 26
    var $shipping; // 1
    var $price; // '419.0000'
    var $points; // 0
    var $tax_class_id; // 0
    var $date_available; // ''
    var $weight; // '550.00'
    var $weight_class_id; // 2
    var $length; // '12.50'
    var $width; // '17.00'
    var $height; // '4.00'
    var $length_class_id; // 1
    var $subtract; // 1
    var $minimum; // 1
    var $sort_order; // 1
    var $status; // 1
    var $viewed; // 0
    var $date_added; // now()
    var $date_modified; // '0000-00-00 00:00:00'
    
    // Конструктор. Заполняем поля из массива POST
    function Product(){
        $this->product_id = $_POST['product_id'];
        $this->model = $_POST['model'];
        $this->sku = $_POST['sku'];
        $this->upc = $_POST['upc'];
        $this->ean = $_POST['ean'];
        $this->jan = $_POST['jan'];
        $this->isbn = $_POST['isbn'];
        $this->mpn = $_POST['mpn'];
        $this->location = $_POST['location'];
        $this->quantity = $_POST['quantity'];
        $this->stock_status_id = $_POST['stock_status_id'];
        $this->image = $_POST['image'];
        $this->manufacturer_id = $_POST['manufacturer_id'];
        $this->shipping = $_POST['shipping'];
        $this->price = $_POST['price'];
        $this->points = $_POST['point'];
        $this->tax_class_id = $_POST['tax_class_id'];
        $this->date_available = $_POST['date_available'];
        $this->weight = $_POST['weight'];
        $this->weight_class_id = $_POST['weight_class_id'];
        $this->length = $_POST['length'];
        $this->width = $_POST['width'];
        $this->height = $_POST['height'];
        $this->length_class_id = $_POST['length_class_id'];
        $this->subtract = $_POST['subtract'];
        $this->minimum = $_POST['minimum'];
        $this->sort_order = $_POST['sort_order'];
        $this->status = $_POST['status'];
        $this->viewed = $_POST['viewed'];
        $this->date_added = $_POST['date_added'];
        $this->date_modified = $_POST['date_modified'];
    }
    
    // Проверяет заполнены ли все нужные поля для добавления товара
    function checkFilds(){
        
    }
    
    // Добавляет продукт. По сути создает запись в таблице oc_product
    function addProductToMysql(){
        
    }
}
