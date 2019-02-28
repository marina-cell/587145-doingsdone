<?php

require_once('data.php');
require_once('functions.php');

// Подключение к MySQL
$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

if (!$link){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    die();
}
