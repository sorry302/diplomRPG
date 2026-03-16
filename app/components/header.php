<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



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
    <div class="logo"><h3>LifeQuest</h3></div>

    <div class="nav">
        
<?php
if(isset($_SESSION['user'])){
?>
<a href="<?= BASE_URL ?>logout.php" class="nav-exit" title="Выход">
            <i data-lucide="door-open"></i>
        </a>
        <script>
            lucide.createIcons();
        </script>
<a href="<?= BASE_URL ?>index.php">Главная</a>
<a href="<?= BASE_URL ?>profile.php">Профиль</a>
<a href="#">Помощь</a>
<?php
if($roleId == 3 && isset($roleId)){
?>
        <a href="/app/admin/admin_panel.php">Админ панель</a>
<?php
}
?>
<?php
}
?>
    </div> 
</header>
<script>
  window.APP_USER = {
        id: <?= json_encode($userId) ?>,
        role: <?= json_encode($roleId) ?>
    };
</script>
