<?php
require_once __DIR__ . '/config/config.php';
require 'app/components/header.php';
?>
<link rel="stylesheet" href="assets/css/main.css">
<main>
  <section class="panel auth-panel">
    <h2 class="panel-title">Регистрация</h2>

    <form action="app/functions/reg_user.php" method="post" class="auth-form">
        <label for="">Логин</label>
        <input type="text" name="username" placeholder="Логин" pattern="[a-zA-Z0-9]+" minlength="5" required>
        <label for="">Электронная почта</label>
        <input type="email" name="email" placeholder="Email" required>
        <label for="">Пароль</label>
        <input type="password" name="password" placeholder="Пароль" minlength="8" required>
        <label for="">Подтверждение пароля</label>
        <input type="password" name="check_pass" placeholder="Подтверждение пароля" minlength="8" required>
        <button type="submit">Создать аккаунт</button>

        <p class="switch-link">Если есть аккаунт, <a href="auth.php">авторизироваться</a></p>

        <?php if(isset($_SESSION['errorRPass'])): ?>
            <p class="error"><?= $_SESSION['errorRPass'] ?></p>
            <?php unset($_SESSION['errorRPass']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['errorRLogin'])): ?>
            <p class="error"><?= $_SESSION['errorRLogin'] ?></p>
            <?php unset($_SESSION['errorRLogin']); ?>
        <?php endif; ?>
    </form>
  </section>
</main>
