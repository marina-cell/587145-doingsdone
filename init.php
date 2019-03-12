<?php

require_once('data.php');
require_once('functions.php');

// Подключение к MySQL
$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);

if ($link) {
    mysqli_set_charset($link, "utf8");
}
else {
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
    exit();
}
