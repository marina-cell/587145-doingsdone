<?php

require_once('init.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: guest.php");
    exit();
}

$cur_user_id = $_SESSION['user']['id'] ?? 0;

// Запросы данных из SQL
$projects = get_projects($link, $cur_user_id);
$all_tasks_count = get_tasks_count($link, $cur_user_id);
$inbox_tasks_count = get_tasks_count($link, $cur_user_id, "\0");

// Проверка GET-параметра на наличие в БД
$pr_id = $_GET['pr_id'] ?? null;
if ($pr_id && !is_correct_project_id($link, $cur_user_id, $pr_id)) {
    http_response_code(404);
    exit();
}

// Статус: показывать или нет выполненные задачи
$show_complete_tasks = $_GET['show_completed'] ?? null;

// Отметка о выполнении задачи
$task_id = $_GET['task_id'] ?? null;
$task_state = $_GET['check'] ?? null;
if ($task_id) {
    update_task_state($link, $task_id, $task_state);
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
    'pr_id' => $pr_id,
    'all_tasks_count' => $all_tasks_count,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
