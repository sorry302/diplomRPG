<?php
session_start();

// Проверка авторизации
$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    header('Location: /rpg-activ/auth.php');
    exit;
}

// Подключение БД
require_once __DIR__ . '/db.php';

// Подключение сервиса StatsInitializer
require_once __DIR__ . '/../services/StatsInitializer.php';

// Получаем данные из формы
$age = (int)($_POST['age'] ?? 0);
$weight = (float)($_POST['weight'] ?? 0);
$height = (float)($_POST['height'] ?? 0);

// Валидация
if ($age <= 0 || $weight <= 0 || $height <= 0) {
    header('Location: /rpg-activ/index.php?error=profile');
    exit;
}

// Расчёт BMI
$heightMeters = $height / 100;
$bmi = round($weight / ($heightMeters * $heightMeters), 2);

// Подготовка профиля для StatsInitializer
$profile = [
    'age' => $age,
    'weight' => $weight,
    'height' => $height,
];

// Транзакция
try {
    $db->beginTransaction();

    // Вставка или обновление профиля пользователя
    $stmt = $db->prepare("
        INSERT INTO user_profile (user_id, age, weight, height, bmi)
        VALUES (:user_id, :age, :weight, :height, :bmi)
        ON DUPLICATE KEY UPDATE
            age = :age,
            weight = :weight,
            height = :height,
            bmi = :bmi
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':age' => $age,
        ':weight' => $weight,
        ':height' => $height,
        ':bmi' => $bmi
    ]);

    // Инициализация статов (StatsInitializer)
    $initializer = new StatsInitializer($db);
    $initializer->init($userId, $profile);

    // Обновление роли пользователя
    $stmt = $db->prepare("UPDATE users SET role = 2 WHERE id = ?");
    $stmt->execute([$userId]);
    $_SESSION['user']['role_id'] = 2;

    // Коммит транзакции
    $db->commit();

    header('Location: /rpg-activ/index.php');
    exit;

} catch (Throwable $e) {
    $db->rollBack();
    echo '<pre>';
    echo "Ошибка при сохранении профиля: " . $e->getMessage();
    exit;
}