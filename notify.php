<?php

require_once('vendor/autoload.php');
require_once('init.php');

$transport = new Swift_SmtpTransport('phpdemo.ru', 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);

$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$tasks_todo = get_tasks_for_today($link);

$users_with_tasks = [];
foreach($tasks_todo as $key => $val) {
    $users_with_tasks[$val['user_id']][] = $val;
}

foreach ($users_with_tasks as $key => $row) {
    $message = new Swift_Message();
    $message->setSubject("Уведомление от сервиса «Дела в порядке»");

    $list = [];
    foreach ($row as $task) {
        $list[] = "'" . $task['task_name'] . "' на " . $task['deadline'];
    }
    $list = implode(",\n", $list);
    $string = count($row) === 1 ? "запланирована задача" : "запланированы задачи";
    $text = "У Вас " . $string . ":\n" . $list;

    $message->setBody("Уважаемый(ая) " . $row[0]['user_name'] . "!\n\n" . $text . ".");
    $message->setFrom(['keks@phpdemo.ru' => 'DoingsDone']);
    $message->setTo([$row[0]['email'] => $row[0]['user_name']]);
    $mailer->send($message);
}
