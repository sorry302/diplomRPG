<?php
//создаем пользователя не персонажа!
session_start(); 
require 'db.php';

if(isset($_POST)){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $check_pass = $_POST['check_pass'];
    $created_at = date('Y-m-d H:i:s');

    // Проверка существующего пользователя с PDO
    $stmt_username = $db->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $stmt_username->execute([$username]);
    
    // Получаем количество строк
    if($stmt_username->rowCount() == 0){
        if($password == $check_pass){
            // Хешируем пароль
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Вставляем нового пользователя
            $stmt = $db->prepare("INSERT INTO `users`(`username`, `email`, `password`, `created_at`) 
                                  VALUES (?,?,?,?)");
            $stmt->execute([$username, $email, $hashed_password, $created_at]);
            
            // Убираем var_dump (он для отладки)
            // var_dump($stmt);
            
            $_SESSION['goodRUser'] = "Успех!";
            header("Location: ../../auth.php");
            exit(); // Добавьте exit после header
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