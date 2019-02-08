<?php

// Шаблонизатор
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = 'Ошибка: файл не найден';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

// Подсчет задач в проекте
function tasks_number ($total_task_list, $project) {
    $number = 0;
    foreach ($total_task_list as $task) {
        if ($project === $task['category']) {
            $number++;
        }
    }
    return $number;
}
