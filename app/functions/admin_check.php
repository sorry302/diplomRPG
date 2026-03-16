<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}

// role = 3 → администратор
if ((int)$_SESSION['user']['role_id'] !== 3) {
    http_response_code(403);
    die('Доступ запрещён');
}

$userId = (int)$_SESSION['user']['user_id'];
$roleId = (int)$_SESSION['user']['role_id'];
