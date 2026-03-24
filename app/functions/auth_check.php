<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка авторизации
if (empty($_SESSION['user']) || empty($_SESSION['user']['user_id'])) {
    header("Location: /auth.php");
    exit;
}

// Безопасно получаем данные
$userId = (int)($_SESSION['user']['user_id'] ?? 0);
$roleId = (int)($_SESSION['user']['role_id'] ?? 0);

// (опционально) подключение БД, если нужно
require_once __DIR__ . '/db.php';