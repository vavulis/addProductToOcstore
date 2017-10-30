<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function inverse($x) {
    if ($x == 0) {
        throw new Exception("Division by zero");
    }
    return (1 / $x);
}

try {
    echo inverse(1) . "\n";
    echo inverse(10) . "\n";
    echo inverse(0) . "\n";
} catch (Exception $ex) {
    echo 'Выброшено исключение: ', var_dump($ex), "\n";
    if ($ex->getMessage()=='Division by zero') {
        echo "allok!";
    }
}


echo date("l");
?>

