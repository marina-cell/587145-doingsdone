<?php

require_once('init.php');

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$cur_user_id = $_SESSION['user']['id'];

// Проверка GET-параметра на наличие в БД
$pr_id = $_GET['pr_id'] ?? null;
if ($pr_id && !is_correct_project_id($link, $cur_user_id, $pr_id)) {
    http_response_code(404);
    exit();
}

// Валидация формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_project = $_POST;

    $required = ['name'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($new_project[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (is_exist_project_name($link, $cur_user_id, $new_project['name'])) {
        $errors['project'] = 'Такой проект уже существует';
    }

    if (!count($errors)) {
        add_new_project($link, $new_project['name'], $cur_user_id);
        exit();
    }
}

$page_content = include_template('project.php', [
    'new_project' => $new_project ?? null,
    'errors' => $errors ?? null
]);

// Запросы данных из SQL
$projects = get_projects($link, $cur_user_id);
$all_tasks_count = get_tasks_count($link, $cur_user_id);
$inbox_tasks_count = get_tasks_count($link, $cur_user_id, "\0");

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление проекта',
    'projects' => $projects,
    'pr_id' => $pr_id,
    'all_tasks_count' => $all_tasks_count,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
