<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: /auth.php");
    exit;
}

$userId = (int)$_SESSION['user']['user_id'];
$roleId = (int)$_SESSION['user']['role_id'];