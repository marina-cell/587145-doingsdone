<?php

require_once('init.php');

// Валидация формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_task = $_POST;
    $new_task['path'] = "";

    $required = ['name'];
    $errors = [];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (!empty($new_task['date']) && !check_date_format($new_task['date'])) {
        $errors['date'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
    } else if (!empty($new_task['date']) && (strtotime($new_task['date']) + 86400) < time()) {
        $errors['date'] = 'Машину времени еще не изобрели';
    }

    if(!empty($new_task['project']) && !is_correct_project_id($link, $cur_user_id, $new_task['project'])) {
        $errors['project'] = 'Такого проекта не существует';
    }

    if (isset($_FILES['preview']) && !empty($_FILES['preview']['name'])) {
        $tmp_name = $_FILES['preview']['tmp_name'];
        $path = $_FILES['preview']['name'];
        move_uploaded_file($tmp_name, 'uploads/' . $path);
        $new_task['path'] = $path;
    }

    if (count($errors)) {
        $page_content = include_template('add.php', ['projects' => $projects, 'new_task' => $new_task, 'errors' => $errors]);
    }
    else {
        $deadline_date = date("Y.m.d", strtotime($new_task['date']) ?? "");
        $new_task['project'] = $new_task['project'] ? $new_task['project'] : "0";
        add_new_task($link, $cur_user_id, $new_task['project'] ?? "0", $new_task['name'], $new_task['path'], $deadline_date);
        $page_content = "";
    }
}
else {
    $page_content = include_template('add.php', [
        'projects' => $projects
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Добавление задачи',
    'projects' => $projects,
    'all_tasks_count' => $all_tasks_count,
    'inbox_tasks_count' => $inbox_tasks_count
]);

print($layout_content);
