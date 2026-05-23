<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

$_SESSION = [];
session_destroy();

header('Location: ' . BASE_PATH . '/login.php');
exit;
