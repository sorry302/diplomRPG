<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        
        $stmt = $db->prepare("SELECT `id`, `username`, `password`, `role` FROM `users` WHERE `username` = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();
            
            // Проверяем пароль
            if (password_verify($password, $user['password'])) {
                // Успешная аутентификация
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
    } catch(PDOException $e) {
        // Логируем ошибку
        error_log("Ошибка базы данных: " . $e->getMessage());
        header("Location: ../../auth.php?error=1");
        exit();
    }
} else {
    // Неправильный запрос
    header("Location: ../../auth.php");
    exit();
}
?>