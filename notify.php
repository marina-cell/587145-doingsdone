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

foreach ($tasks_todo as $task) {
    $recipient[$task['email']] = $task['user_name'];

    $message = new Swift_Message();
    $message->setSubject("Уведомление от сервиса «Дела в порядке»");
    $message->setBody("Уважаемый(ая) " . $task['user_name'] . "! У Вас запланирована задача: '" . $task['task_name'] . "' на " . $task['deadline'] . ".");
    $message->setFrom(['keks@phpdemo.ru' => 'DoingsDone']);
    $message->setTo($recipient);

    $mailer->send($message);
}
