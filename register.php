<?php

require_once('init.php');

// Запросы данных из SQL
$projects = get_projects($link, $cur_user_id);
$all_tasks_count = get_tasks_count($link, $cur_user_id);
$inbox_tasks_count = get_tasks_count($link, $cur_user_id, "\0");

// Проверка GET-параметра на наличие в БД
$pr_id = $_GET['pr_id'] ?? null;
if ($pr_id && !is_correct_project_id($link, $cur_user_id, $pr_id)) {
    http_response_code(404);
    die();
}

$tpl_data = [];

// Валидация формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;

    $required_fields = ['email', 'password', 'name'];
    $errors = [];

    foreach ($required_fields as $key) {
        if (empty($user[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email_format'] = 'Email должен быть корректным';
    }

    if (empty($errors)) {
        if (is_email_exists($link, $user['email'])) {
            $errors['email_double'] = 'Пользователь с этим email уже зарегистрирован';
        }
        else {
            $password = password_hash($user['password'], PASSWORD_DEFAULT);
            add_new_user($link, $user['email'], $user['name'], $password);
            exit();
        }
    }

    $tpl_data['user'] = $user;
    $tpl_data['errors'] = $errors;
}

$page_content = include_template('register.php', $tpl_data);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация аккаунта',
    'projects' => $projects,
    'pr_id' => $pr_id,
    'all_tasks_count' => $all_tasks_count,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
