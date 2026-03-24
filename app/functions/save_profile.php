<?php
session_start();

// Проверка авторизации
$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    header('Location: /auth.php');
    exit;
}

// Подключение БД
require_once __DIR__ . '/db.php';

// Подключение StatsInitializer
require_once __DIR__ . '/../services/StatsInitializer.php';

$userId = (int)$userId;

// Получаем данные из формы
$age    = (int)($_POST['age'] ?? 0);
$weight = (float)($_POST['weight'] ?? 0);
$height = (float)($_POST['height'] ?? 0);

if ($age <= 0 || $weight <= 0 || $height <= 0) {
    header('Location: /index.php?error=profile');
    exit;
}

$profile = [
    'age' => $age,
    'weight' => $weight,
    'height' => $height,
];

// Считаем BMI
$bmi = round($weight / (($height / 100) ** 2), 2);

mysqli_begin_transaction($conn);

try {

    // Вставка/обновление профиля
    mysqli_query($conn, "
        INSERT INTO user_profile (user_id, age, weight, height, bmi)
        VALUES ($userId, $age, $weight, $height, $bmi)
        ON DUPLICATE KEY UPDATE
            age = $age,
            weight = $weight,
            height = $height,
            bmi = $bmi
    ");

    // Инициализация статов (ВАЖНО: меняем $db → $conn)
    $initializer = new StatsInitializer($conn);
    $initializer->init($userId, $profile);

    // Обновление роли
    mysqli_query($conn, "
        UPDATE users SET role = 2 WHERE id = $userId
    ");

    $_SESSION['user']['role_id'] = 2;

    mysqli_commit($conn);

    header('Location: /index.php');
    exit;

} catch (Throwable $e) {
    mysqli_rollback($conn);
    echo 'Ошибка: ' . $e->getMessage();
    exit;
}