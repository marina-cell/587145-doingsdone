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
