<?php

require_once('init.php');

$page_content = include_template('guest.php', []);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'without_sidebar' => true
]);

print($layout_content);
