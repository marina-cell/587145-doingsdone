<?php

require_once('init.php');

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
    'all_tasks_count' => $all_tasks_count,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
