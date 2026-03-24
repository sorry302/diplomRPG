<?php
// создаем пользователя (не персонажа)
session_start(); 
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $check_pass = $_POST['check_pass'];
    $created_at = date('Y-m-d H:i:s');

    // Минимальная защита
    $username = mysqli_real_escape_string($conn, $username);
    $email    = mysqli_real_escape_string($conn, $email);

    // Проверка существующего пользователя
    $result = mysqli_query($conn, "
        SELECT id FROM users WHERE username = '$username'
    ");

    if ($result && mysqli_num_rows($result) == 0) {

        if ($password === $check_pass) {

            // Хешируем пароль
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Вставка пользователя
            mysqli_query($conn, "
                INSERT INTO users (username, email, password, created_at)
                VALUES ('$username', '$email', '$hashed_password', '$created_at')
            ");

            $_SESSION['goodRUser'] = "Успех!";
            header("Location: ../../auth.php");
            exit();

        } else {
            $_SESSION['errorRPass'] = "Пароли не совпадают";
            header("Location: ../../reg.php");
            exit();
        }

    } else {
        $_SESSION['errorRLogin'] = "Такой пользователь существует";
        header("Location: ../../reg.php");
        exit();
    }
}
?>