<?php

require_once('mysql_helper.php');

/**
 * Шаблонизатор
 * @param string    $name   Название шаблона
 * @param array     $data   Данные для передачи в шаблон
 *
 * @return string|false     Контент
 */
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

/**
 * Проверяет, что переданная дата приближается к дедлайну
 * @param string    $date   Дата дедлайна
 *
 * @return bool
 */
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

/**
 * Безопасное получение данных из БД MySQL (с помощью подготовленных выражений)
 * @param  mysqli    $link          Ресурс соединения
 * @param  string    $sql           SQL запрос с плейсхолдерами вместо значений
 * @param  array     $query_data    Данные для вставки на место плейсхолдеров
 *
 * @return array
 */
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


/**
 * Безопасная запись данных в БД MySQL (с помощью подготовленных выражений)
 * @param  mysqli    $link   Ресурс соединения
 * @param  string    $sql    SQL запрос с плейсхолдерами вместо значений
 * @param  array     $data    Данные для вставки на место плейсхолдеров
 *
 * @return int|string|bool
 */
function db_insert_data ($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $result = mysqli_insert_id($link);
    }

    return $result;
}

/**
 * Возвращает список проектов для указанного пользователя
 * @param  mysqli    $link       Ресурс соединения
 * @param  int       $user_id    ID пользователя
 *
 * @return array
 */
function get_projects ($link, $user_id) {
    $sql = 'SELECT project.id, project.name, (SELECT COUNT(*) FROM task WHERE task.project_id = project.id AND task.state = 0) AS tasks_count
              FROM project
             WHERE user_id = ?
          ORDER BY project.id;';

    $projects = db_fetch_data($link, $sql, [$user_id]);

    return $projects;
}

/**
 * Добавляет новый проект в базу
 * @param  mysqli    $link       Ресурс соединения
 * @param  string    $name       Название проекта
 * @param  int       $user_id    ID пользователя
 */
function add_new_project ($link, $name, $user_id) {
    $sql = 'INSERT INTO project (name, user_id)
              VALUES (?, ?)';
    $res = db_insert_data($link, $sql, [$name, $user_id]);

    if($res) {
        header("Location: index.php");
    }
    else {
        print("Ошибка при записи в базу данных");
    }
}

/**
 * Проверяет, что переданный ID проекта существует в базе
 * @param  mysqli    $link       Ресурс соединения
 * @param  int       $user_id    ID пользователя
 * @param  int       $pr_id      ID проекта
 *
 * @return bool
 */
function is_correct_project_id ($link, $user_id, $pr_id) {
    $sql = 'SELECT id, user_id
              FROM project
             WHERE id = ?
               AND user_id = ?;';
    $res = ($pr_id == 'inbox') ? true : sizeof(db_fetch_data($link, $sql, [$pr_id, $user_id]));

    return $res;
}

/**
 * Проверяет, что переданное название проекта существует в базе
 * @param  mysqli    $link       Ресурс соединения
 * @param  int       $user_id    ID пользователя
 * @param  string    $name       Название проекта
 *
 * @return bool
 */
function is_exist_project_name ($link, $user_id, $name) {
    $sql = 'SELECT name
              FROM project
             WHERE name = ?
               AND user_id = ?;';

    return sizeof(db_fetch_data($link, $sql, [$name, $user_id]));
}

/**
 * Возвращает список задач
 * @param  mysqli    $link       Ресурс соединения
 * @param  int       $user_id    ID пользователя
 * @param  int       $pr_id      ID проекта
 * @param  bool      $is_show    Статус: показывать или нет завершенные задачи
 * @param  string    $filter     Условие фильтрации задач
 *
 * @return array
 */
function get_tasks ($link, $user_id, $pr_id, $is_show, $filter) {
    $data = [$user_id];
    $additional_conditions = ' ';

    if($pr_id) {
        $additional_conditions .= ' AND t.project_id = ? '; // если задан ID проекта
        $data[] = $pr_id;
    }
    if(!$is_show) {
        $additional_conditions .= ' AND t.state = 0 ';      // если нужно скрыть завершенные задачи (state = 1)
    }
    switch ($filter) {                                    // фильтр задач по сроку выполнения
        case 'agenda':
            $additional_conditions .= ' AND t.deadline = CURDATE() ';
            break;
        case 'tomorrow':
            $additional_conditions .= ' AND t.deadline <= (CURDATE()+1) AND t.deadline > CURDATE() ';
            break;
        case 'overdue':
            $additional_conditions .= ' AND t.deadline < CURDATE() ';
            break;
    }

    $sql = 'SELECT t.id, t.name AS task_name, t.state, t.deadline, t.file 
              FROM task t
             WHERE t.user_id = ?
                   ' . $additional_conditions . '
          ORDER BY t.deadline';

    return db_fetch_data($link, $sql, $data);
}

/**
 * Возвращает количество задач (всего или для указанного проекта)
 * @param  mysqli    $link       Ресурс соединения
 * @param  int       $user_id    ID пользователя
 * @param  int       $pr_id      ID проекта
 *
 * @return int
 */
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

/**
 * Добавляет новую задачу в базу
 * @param mysqli    $link       Ресурс соединения
 * @param int       $user_id    ID пользователя
 * @param int       $pr_id      ID проекта
 * @param string    $task_name  Название задачи
 * @param string    $file_path  Ссылка на файл, загруженный пользователем
 * @param string    $deadline   Дата, до которой задача должна быть выполнена
 */
function add_new_task ($link, $user_id, $pr_id, $task_name, $file_path, $deadline) {
    if ($deadline) {
        $sql = 'INSERT INTO task (date_create, date_done, state, name, file, user_id, project_id, deadline)
              VALUES (NOW(), NULL, 0, ?, ?, ?, ?, ?)';
        $res = db_insert_data($link, $sql, [$task_name, $file_path, $user_id, $pr_id, $deadline]);
    }
    else {
        $sql = 'INSERT INTO task (date_create, date_done, state, name, file, deadline, user_id, project_id)
              VALUES (NOW(), NULL, 0, ?, ?, NULL, ?, ?)';
        $res = db_insert_data($link, $sql, [$task_name, $file_path, $user_id, $pr_id]);
    }

    if($res) {
        header("Location: index.php");
    }
    else {
        print("Ошибка при записи в базу данных");
    }
}

/**
 * Обновляет статус задачи
 * @param mysqli    $link       Ресурс соединения
 * @param int       $user_id    ID пользователя
 * @param int       $task_id    ID задачи
 * @param int       $task_state Статус задачи (0 - не завершена, 1 - завершена)
 */
function update_task_state ($link, $user_id, $task_id, $task_state) {
    $sql = 'UPDATE task SET state = ?, date_done = NOW() WHERE id = ? AND user_id = ?';
    db_insert_data($link, $sql, [$task_state, $task_id, $user_id]);
    header("Location: index.php");
}

/**
 * Возвращает данные о пользователе по адресу его электронной почты
 * @param mysqli    $link       Ресурс соединения
 * @param string    $email      Электронная почта
 *
 * @return array|null
 */
function get_user ($link, $email) {
    $sql = 'SELECT *
              FROM user
             WHERE email = ?';

    $user = db_fetch_data($link, $sql, [$email]);

    return $user[0] ?? null;
}

/**
 * Добавляет нового пользователя в базу
 * @param mysqli    $link       Ресурс соединения
 * @param string    $email      Электронная почта
 * @param string    $name       Имя пользователя
 * @param string    $password   Пароль
 */
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
 * @param string    $date   Строка с датой
 *
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
