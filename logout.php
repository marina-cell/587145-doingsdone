<?php

require_once('init.php');

session_start();

$_SESSION = [];

header("Location: index.php");
