<?php
require_once __DIR__ . '/config/config.php';

require 'app/components/header.php';
?>
<link rel="stylesheet" href="assets/css/main.css">
    <main>
        <section class="panel auth-panel">
            <h2 class="panel-title">Авторизация</h2>
        <form action="app/functions/auth_user.php" method="post" class="auth-form">
            <label for="">Логин</label>
            <input type="text" name="username" id="" pattern="[a-zA-Z0-9]+" minlength="5" required>
            <label for="">Пароль</label>
            <input type="password" name="password" id="" minlength="8" required>
            <input type="submit" name="" id="">
            <p class="switch-link">Если нет аккаунта,<a href="reg.php">создать новый</a></p>
            
             <?php
            if(isset($_SESSION['errorAuth'])){
            ?>
                <p style="color: red;"><?= $_SESSION['errorAuth'] ?></p>
            <?php
            unset($_SESSION['errorAuth']);
            }
            ?>
            <?php
            if(isset($_SESSION['goodRUser']) ){
            ?>
                <p style="color: green;"><?= $_SESSION['goodRUser'] ?></p>
            <?php
            unset($_SESSION['goodRUser']);
            }
            ?>
        </form>
        </section>
    </main>
</body>
</html>