<?php
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../services/ExperienceService.php';
require_once __DIR__ . '/../services/AchievementService.php';

$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    header('Location: /auth.php');
    exit;
}

$userId  = (int)$userId;
$foodId   = (int)($_POST['food_id'] ?? 0);
$portions = max(1, (int)($_POST['portions'] ?? 1));

// Получаем еду
$result = mysqli_query($conn, "SELECT * FROM foods WHERE id = $foodId");
$food = mysqli_fetch_assoc($result);

if (!$food) {
    header('Location: /index.php');
    exit;
}

// Изменения
$healthChange = $food['health_change'] * $portions;
$fatChange    = $food['obesity_change'] * $portions;
$expChange    = 1 * $portions;

// Влияние жира
$physicalChange = (int) round(-$fatChange / 2);

// Начинаем транзакцию
mysqli_begin_transaction($conn);

try {

    // Опыт (ВАЖНО: нужно передать $conn вместо $db)
    $expService = new ExperienceService($conn);
    $expService->addExp($userId, $expChange);

    // Лог еды
    mysqli_query($conn, "
        INSERT INTO food_logs (user_id, food_id, portions)
        VALUES ($userId, $foodId, $portions)
    ");

    // Здоровье
    mysqli_query($conn, "
        INSERT INTO user_stats (user_id, stat_code, value)
        VALUES ($userId, 'health', $healthChange)
        ON DUPLICATE KEY UPDATE
        value = LEAST(100, GREATEST(0, value + VALUES(value)))
    ");

    // Жир
    mysqli_query($conn, "
        INSERT INTO user_stats (user_id, stat_code, value)
        VALUES ($userId, 'fat', $fatChange)
        ON DUPLICATE KEY UPDATE
        value = LEAST(100, GREATEST(0, value + VALUES(value)))
    ");

    // Физическая форма
    if ($physicalChange !== 0) {
        mysqli_query($conn, "
            INSERT INTO user_stats (user_id, stat_code, value)
            VALUES ($userId, 'physical', $physicalChange)
            ON DUPLICATE KEY UPDATE
            value = LEAST(100, GREATEST(0, value + VALUES(value)))
        ");
    }

    mysqli_commit($conn);

    // Достижения (тоже меняем $db → $conn)
    $achievement = new AchievementService($conn, $userId);
    $achievement->checkAll();

    $_SESSION['action_feedback'] = [
        ['label' => 'Здоровье', 'value' => $healthChange],
        ['label' => 'Жир', 'value' => $fatChange],
        ['label' => 'Физическая форма', 'value' => $physicalChange],
        ['label' => 'Опыт', 'value' => $expChange],
    ];

    header('Location: /index.php');
    exit;

} catch (Throwable $e) {
    mysqli_rollback($conn);
    echo "<pre>Ошибка: {$e->getMessage()}</pre>";
    exit;
}