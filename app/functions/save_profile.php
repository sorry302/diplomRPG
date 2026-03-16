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

// Получаем данные из формы
$age = (int)($_POST['age'] ?? 0);
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

try {
    $db->beginTransaction();

    // Вставка/обновление профиля
    $stmt = $db->prepare("
        INSERT INTO user_profile (user_id, age, weight, height, bmi)
        VALUES (:user_id, :age, :weight, :height, :bmi)
        ON DUPLICATE KEY UPDATE
            age = :age,
            weight = :weight,
            height = :height,
            bmi = :bmi
    ");
    $bmi = round($weight / (($height / 100) ** 2), 2);
    $stmt->execute([
        ':user_id' => $userId,
        ':age' => $age,
        ':weight' => $weight,
        ':height' => $height,
        ':bmi' => $bmi
    ]);

    // Инициализация статов
    $initializer = new StatsInitializer($db);
    $initializer->init($userId, $profile);

    // Обновление роли
    $stmt = $db->prepare("UPDATE users SET role = 2 WHERE id = ?");
    $stmt->execute([$userId]);
    $_SESSION['user']['role_id'] = 2;

    $db->commit();

    header('Location: /index.php');
    exit;

} catch (Throwable $e) {
    $db->rollBack();
    echo 'Ошибка: ' . $e->getMessage();
    exit;
}
