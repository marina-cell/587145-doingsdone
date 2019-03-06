<?php

require_once('init.php');

session_start();

$tpl_data = [];

// Валидация формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_user = $_POST;

    $required_fields = ['email', 'password', 'name'];
    $errors = [];

    foreach ($required_fields as $key) {
        if (empty($form_user[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (!filter_var($form_user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email_format'] = 'Email должен быть корректным';
    }

    if (empty($errors)) {
        if (!empty(get_user($link, $form_user['email']))) {
            $errors['email_double'] = 'Пользователь с этим email уже зарегистрирован';
        }
        else {
            $password = password_hash($form_user['password'], PASSWORD_DEFAULT);
            add_new_user($link, $form_user['email'], $form_user['name'], $password);

            $user = get_user($link, $form_user['email']);
            $user = $user[0] ?? null;
            $_SESSION['user'] = $user;
            exit();
        }
    }

    $tpl_data['user'] = $form_user;
    $tpl_data['errors'] = $errors;
}

$page_content = include_template('register.php', $tpl_data);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация аккаунта'
]);

print($layout_content);
