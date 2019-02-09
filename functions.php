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

// Проверка даты на приближение к дедлайну
function is_date_important ($date) {
    $deadline_span = 86400; // секунд в 24 часах

    if(strlen($date) > 0) {
        $date_ts = strtotime($date);
        $ts = time();

        if($date_ts - $ts < $deadline_span) {
            return true;
        }
    }

    return false;
}
