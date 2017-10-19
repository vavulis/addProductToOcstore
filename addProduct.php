<!--1. Добавляем продукт
INSERT INTO `oc_product` (`product_id`, `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`, `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`, `price`, `points`, `tax_class_id`, `date_available`, `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`, `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`) VALUES
('', '7938', '', '', '', '', '978-5-94759-201-6', '', '', 6, 7, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-main.jpg', 26, 1, '419.0000', 0, 0, '', '550.00', 2, '12.50', '17.00', '4.00', 1, 1, 1, 1, 1, 0, now(), '0000-00-00 00:00:00');

2. Добавляем описание
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_h1`, `meta_description`, `meta_keyword`) VALUES
(1, 1, 'Евангельские беседы на каждый день года по церковным зачалам', '&lt;p&gt;Книга «Евангельские беседы на каждый день» включает в себя толкования на Евангелие святителя Иоанна Златоустого, блаженного Феофилакта Болгарского, святителя Феофана Затворника и других святых отцов, а также подробные исторические комментарии о быте, законах и обычаях современников земной жизни Спасителя. Толкования расположены по ежедневным Евангельским чтениям (церковным зачалам).&lt;/p&gt;\r\n\r\n&lt;p&gt;Издание будет интересно всем православным христианам: и мирянам — для чтения дома, в семье, и священнослужителям — для произнесения проповедей, и учащимся церковных учебных заведений.&lt;/p&gt;\r\n\r\n&lt;p&gt;Рекомендовано к публикации Издательским Советом Русской Православной Церкви.&lt;/p&gt;\r\n', '', 'Евангельские беседы на каждый день года по церковным зачалам. Православные книги почтой в магазине http://shop.konstantinsemenov.com', 'Евангельские беседы на каждый день года по церковным зачалам', 'Книга «Евангельские беседы на каждый день» включает в себя толкования на Евангелие святителя Иоанна Златоустого, блаженного Феофилакта Болгарского, святителя Феофана Затворника и других святых отцов, а также подробные исторические комментарии о быте, зако', 'Евангелие, Иоанн Златоуст, Толкования');


3. Добавляем дополнительные картинки
INSERT INTO `oc_product_image` (`product_image_id`, `product_id`, `image`, `sort_order`) VALUES
('', 1, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-1.jpg', ''),
('', 1, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-2.jpg', ''),
('', 1, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-3.jpg', ''),
('', 1, 'catalog/images/evangelskie-besedy-na-kazhdyj-den-goda-po-cerkovnym-zachalam-4.jpg', '');


4. Указываем категории товара
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`, `main_category`) VALUES
(1, 75, 1);


5. Назначаем layout
INSERT INTO `oc_product_to_layout` (`product_id`, `store_id`, `layout_id`) VALUES
(1, 0, 0);


6. Указываем, что товар в будет продаваться в этом магазине
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES
(1, 0);-->
<?php

//1. Добавляем продукт

//2. Добавляем описание

//3. Добавляем дополнительные картинки

//4. Указываем категории товара

//5. Назначаем layout

//6. Указываем, что товар в будет продаваться в этом магазине

// Все пункты надо обернуть в одну транзакцию

// sandbox
$post = R::dispense( 'post' );
$post->title = 'My holiday';
$id = R::store( $post );

?>