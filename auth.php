<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
require 'app/components/header.php';
?>

<link rel="stylesheet" href="assets/css/main.css">

<main>
    <section class="panel auth-panel">
        <h2 class="panel-title">Авторизация</h2>

        <form action="app/functions/auth_user.php" method="post" class="auth-form" autocomplete="off">

            <label for="username">Логин</label>
            <input  type="text"  name="username"  id="username" pattern="[a-zA-Z0-9]+"  minlength="5"  required
            >

            <label for="password">Пароль</label>
            <input  type="password"  name="password"  id="password" minlength="8"  required
            >

            <button type="submit">Войти</button>

            <p class="switch-link">
                Если нет аккаунта, 
                <a href="reg.php">создать новый</a>
            </p>

            <?php if (!empty($_SESSION['errorAuth'])): ?>
                <p style="color: red;">
                    <?= htmlspecialchars($_SESSION['errorAuth']) ?>
                </p>
                <?php unset($_SESSION['errorAuth']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['goodRUser'])): ?>
                <p style="color: green;">
                    <?= htmlspecialchars($_SESSION['goodRUser']) ?>
                </p>
                <?php unset($_SESSION['goodRUser']); ?>
            <?php endif; ?>

        </form>
    </section>
</main>

</body>
</html>