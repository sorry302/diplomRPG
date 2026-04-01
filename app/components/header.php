<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user']['user_id'] ?? null;
$roleId = $_SESSION['user']['role_id'] ?? null;


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeQuest</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>

<body>
<header>
    <div class="logo">
        <h3>LifeQuest</h3>
    </div>

    <div class="nav">
        
        <?php if ($userId): ?>

            <a href="<?= BASE_URL ?>logout.php" class="nav-exit" title="Выход">
                <i data-lucide="door-open"></i>
            </a>

            <a href="<?= BASE_URL ?>index.php">Главная</a>
            <a href="<?= BASE_URL ?>profile.php">Профиль</a>
            <!-- <a href="#">Помощь</a> -->

            <?php if ($roleId == 3): ?>
                <a href="/app/admin/admin_panel.php">Админ панель</a>
            <?php endif; ?>
        <?php endif; ?>

    </div> 
</header>

<script>
    // безопасно передаём в JS
    window.APP_USER = {
        id: <?= json_encode($userId ?? 0) ?>,
        role: <?= json_encode($roleId ?? 0) ?>
    };

    lucide.createIcons();
</script>