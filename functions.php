<?php

require_once('mysql_helper.php');

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

// Проверка даты на приближение к дедлайну
function is_date_important ($date) {
    $deadline_span = 86400; // секунд в 24 часах
    $is_deadline = false;

    if(strlen($date) > 0) {
        $date_ts = strtotime($date);
        $ts = time();

        if($date_ts - $ts < $deadline_span) {
            $is_deadline = true;
        }
    }

    return $is_deadline;
}

// Безопасное получение данных из БД MySQL (с помощью подготовленных выражений)
function db_fetch_data ($link, $sql, $query_data = []) {
    $result_data = [];

    $stmt = db_get_prepare_stmt($link, $sql, $query_data);
    mysqli_stmt_execute($stmt);

    if ($res = mysqli_stmt_get_result($stmt)) {
        $result_data = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    else {
        print("Ошибка при выполнении запроса: " . mysqli_error($link));
    }

    return $result_data;
}

function get_projects ($link, $user_id) {
    return db_fetch_data($link,
        'SELECT p.id, p.name, COUNT(t.name) AS tasks_count
            FROM task t JOIN project p
              ON t.project_id  = p.id
           WHERE t.user_id = ?
           GROUP BY t.project_id 
           ORDER BY p.name;',
        [$user_id]);
}

function get_tasks ($link, $user_id, $pr_id = null) {
    if(isset($pr_id)) {
        $tasks = db_fetch_data($link,
            'SELECT *, task.name AS task_name, project.name AS project_name 
              FROM task JOIN project
             WHERE project.id = task.project_id AND task.project_id = ?
               AND task.user_id = ?',
            [$pr_id, $user_id]);
    } else {
        $tasks = db_fetch_data($link,
            'SELECT *, task.name AS task_name, project.name AS project_name 
              FROM task JOIN project
             WHERE project.id = task.project_id
               AND task.user_id = ?',
            [$user_id]);
    }
    return $tasks;
}
