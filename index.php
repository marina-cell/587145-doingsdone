<?php

require_once('init.php');

// Запросы данных из SQL
$projects = get_projects($link, $cur_user_id);
$inbox_tasks_count = get_inbox_tasks_count($link, $cur_user_id);

// Проверка GET-параметра на наличие в БД
$pr_id = $_GET['pr_id'] ?? null;
if ($pr_id && !is_correct_project_id($link, $cur_user_id, $pr_id)) {
    http_response_code(404);
    die();
}

$task_list = get_tasks($link, $cur_user_id, $pr_id, $show_complete_tasks);

// Шаблонизация
$page_content = include_template('index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'task_list' => $task_list
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'projects' => $projects,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
