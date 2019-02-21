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
$all_tasks = get_tasks($link, $cur_user_id);
$task_list_for_project = $all_tasks;

if (isset($_GET['pr_id'])) {
    $task_list_for_project = get_tasks($link, $cur_user_id, $_GET['pr_id']);

    if(!sizeof($task_list_for_project)) {
        http_response_code(404);
        die();
    }
}

// Шаблонизация
$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_list' => $task_list_for_project
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'projects' => $projects,
    'task_list' => $all_tasks
]);

print($layout_content);
