<?php

require_once('data.php');
require_once('functions.php');

// Подключение к MySQL
$link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($link, "utf8");

if (!$link){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    // Запрос списка проектов для текущего пользователя
    $sql = 'SELECT * FROM project WHERE user_id = ' . $cur_user_id;

    if ($result = mysqli_query($link, $sql)) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        print("Ошибка при выполнении запроса: " . mysqli_error($link));
    }

    $sql = 'SELECT *, task.name AS task_name, project.name AS project_name FROM task JOIN project WHERE project.id = task.project_id AND task.user_id = ' .$cur_user_id;

    if ($result = mysqli_query($link, $sql)) {
        $task_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else {
        print("Ошибка при выполнении запроса: " . mysqli_error($link));
    }
}

$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_list' => $task_list
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'projects' => $projects,
    'task_list' => $task_list
]);

print($layout_content);
