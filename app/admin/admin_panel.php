<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>

<main class="admin">
    <h1>Админ-панель</h1>

    <ul class="admin-menu">
        <li><a href="achievements.php">🏆 Достижения</a></li>
        <!-- позже добавим: еда, активности, пользователи -->
    </ul>
</main>
