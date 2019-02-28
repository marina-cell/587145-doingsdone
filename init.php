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

// Запросы данных из SQL
$projects = get_projects($link, $cur_user_id);
$all_tasks_count = get_tasks_count($link, $cur_user_id);
$inbox_tasks_count = get_tasks_count($link, $cur_user_id, "\0");
