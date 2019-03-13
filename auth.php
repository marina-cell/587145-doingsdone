<?php

require_once('init.php');

session_start();

// Валидация формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_user = $_POST;

    $required_fields = ['email', 'password'];
    $errors = [];

    foreach ($required_fields as $key) {
        if (empty($form_user[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (isset($form_user['email'])) {
        if (!filter_var($form_user['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email_format'] = 'Email должен быть корректным';
        }
    }

    if(!count($errors)) {
        $user = get_user($link, $form_user['email']);

        if (!$user) {
            $errors['email_invalid'] = 'Такой пользователь не найден';
        }
        else if (password_verify($form_user['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        }
        else {
            $errors['password_invalid'] = 'Неверный пароль';
        }
    }
}

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$page_content = include_template('auth.php', ['user' => $form_user ?? null, 'errors' => $errors ?? null]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Аутентификация'
]);

print($layout_content);
