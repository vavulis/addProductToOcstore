<?php

$db = mysql_connect("127.0.0.1", "semenoh3_tst", "123456");
mysql_select_db("semenoh3_tst");
mysql_query("SET NAMES utf8");
$_POST[name] = mysql_real_escape_string($_POST[name]);
$_POST[price] = mysql_real_escape_string($_POST[price]);
$_POST[description] = mysql_real_escape_string($_POST[description]);
$_POST[image] = mysql_real_escape_string($_POST[image]);
mysql_query("INSERT INTO product (name, price, description, image) VALUES ('$_POST[name]', '$_POST[price]', '$_POST[description]', '$_POST[image]')");

?>