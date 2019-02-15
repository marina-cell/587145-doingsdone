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
$projects = db_fetch_data($link,
    'SELECT p.name, COUNT(t.name) AS tasks_count
            FROM project p JOIN task t 
              ON p.id = t.project_id 
             AND p.user_id = ?
           GROUP BY p.name 
           ORDER BY p.name;',
    [$cur_user_id]
);

$task_list = db_fetch_data($link,
    'SELECT *, task.name AS task_name, project.name AS project_name 
            FROM task JOIN project
           WHERE project.id = task.project_id
             AND task.user_id = ?',
    [$cur_user_id]
);


// Шаблонизация
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
