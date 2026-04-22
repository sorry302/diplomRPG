<?php
session_start();
require 'db.php';

if (isset($_POST['username'], $_POST['password'])) {
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $username = mysqli_real_escape_string($conn, $username);

    $query = "SELECT `id`, `username`, `password`, `role` 
              FROM `users` 
              WHERE `username` = '$username'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Проверяем пароль
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role_id' => $user['role']
            ];

            // Регенерация ID сессии
            session_regenerate_id(true);

            header("Location: ../../index.php");
            exit();

        } else {
            // Неверный пароль
            sleep(1);
            header("Location: ../../auth.php?error=1");
            exit();
        }

    } else {
        // Пользователь не найден
        sleep(1);
        header("Location: ../../auth.php?error=1");
        exit();
    }

} else {
    // Неправильный запрос
    header("Location: ../../auth.php");
    exit();
}
?>