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


// Безопасная запись данных в БД MySQL (с помощью подготовленных выражений)
function db_insert_data ($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($link);
    }

    return $result;
}

function is_correct_project_id ($link, $user_id, $pr_id) {
    $sql = 'SELECT id, user_id
              FROM project
             WHERE id = ?
               AND user_id = ?;';
    $res = ($pr_id == 'inbox') ? true : sizeof(db_fetch_data($link, $sql, [$pr_id, $user_id]));

    return $res;
}

function get_projects ($link, $user_id) {
    $sql = 'SELECT p.id, p.name, COUNT(t.name) AS tasks_count
              FROM task t JOIN project p
                ON t.project_id  = p.id
             WHERE t.user_id = ?
               AND t.state = 0
          GROUP BY t.project_id
          ORDER BY p.name;';

    return db_fetch_data($link, $sql, [$user_id]);
}

function get_tasks ($link, $user_id, $pr_id = null, $is_show) {
    $data = [$user_id];
    $additional_conditions = ' ';

    if($pr_id) {
        $additional_conditions .= ' AND t.project_id = ? '; // если задан ID проекта
        $data[] = $pr_id;
    }
    if(!$is_show) {
        $additional_conditions .= ' AND t.state = 0 ';     // если нужно скрыть завершенные задачи (state = 1)
    }

    $sql = 'SELECT t.name AS task_name, t.state, t.deadline, t.file 
              FROM task t
             WHERE t.user_id = ?
                   ' . $additional_conditions . '
          ORDER BY t.deadline';

    return db_fetch_data($link, $sql, $data);
}

function get_tasks_count ($link, $user_id, $pr_id = null) {
    $data = [$user_id];
    $additional_conditions = ' ';

    if($pr_id) {
        $additional_conditions .= ' AND t.project_id = ? '; // если задан ID проекта
        $data[] = $pr_id;
    }

    $sql = 'SELECT COUNT(t.name) AS tasks_count
              FROM task t
             WHERE t.user_id = ?
               AND t.state = 0
                   ' . $additional_conditions;

    $array = db_fetch_data($link, $sql, $data);

    return $array[0]['tasks_count'];
}

function add_new_task ($link, $user_id, $pr_id, $task_name, $file_path, $deadline) {
    $sql = 'INSERT INTO task (date_create, date_done, state, name, file, deadline, user_id, project_id)
              VALUES (NOW(), NULL, 0, ?, ?, ?, ?, ?)';
    $res = db_insert_data($link, $sql, [$task_name, $file_path, $deadline, $user_id, $pr_id]);

    if($res) {
        header("Location: index.php");
    }
    else {
        print("Ошибка при записи в базу данных");
    }
}

function is_email_exists ($link, $email) {
    $sql = 'SELECT id
              FROM user
             WHERE email = ?';
    $res = db_fetch_data($link, $sql, [$email]);

    return !empty($res);
}

function add_new_user ($link, $email, $name, $password) {
    $sql = 'INSERT INTO user (date_regist, email, name, password)
              VALUES (NOW(), ?, ?, ?)';
    $res = db_insert_data($link, $sql, [$email, $name, $password]);

    if($res) {
        header("Location: index.php");
    }
    else {
        print("Ошибка при записи в базу данных");
    }
}

/**
 * Проверяет, что переданная дата соответствует формату ДД.ММ.ГГГГ
 * @param string $date строка с датой
 * @return bool
 */
function check_date_format($date) {
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    return $result;
}
