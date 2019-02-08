<?php

// Шаблонизатор
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

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

// Фильтр для защиты от XSS
function filter_usr_data($string) {
    return htmlspecialchars($string);
}
