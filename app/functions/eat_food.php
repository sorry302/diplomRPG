<?php
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../services/ExperienceService.php';
require_once __DIR__.'/../services/AchievementService.php';







$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    header('Location: /auth.php');
    exit;
}





$foodId   = (int)($_POST['food_id'] ?? 0);
$portions = max(1, (int)($_POST['portions'] ?? 1));

// Получаем еду
$stmt = $db->prepare("SELECT * FROM foods WHERE id = ?");
$stmt->execute([$foodId]);
$food = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$food) {
    header('Location: /index.php');
    exit;
}

// Изменения
$healthChange = $food['health_change'] * $portions;
$fatChange    = $food['obesity_change'] * $portions;
$expChange    = 1 * $portions;

// Влияние жира на физическую форму
$physicalChange = (int) round(-$fatChange / 2);

try {
    $db->beginTransaction();

    // Опыт
    $expService = new ExperienceService($db);
    $expService->addExp($userId, $expChange);

    // Лог еды
    $stmt = $db->prepare("
        INSERT INTO food_logs (user_id, food_id, portions)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $foodId, $portions]);

    // Здоровье
    $stmt = $db->prepare("
        INSERT INTO user_stats (user_id, stat_code, value)
        VALUES (?, 'health', ?)
        ON DUPLICATE KEY UPDATE
        value = LEAST(100, GREATEST(0, value + VALUES(value)))
    ");
    $stmt->execute([$userId, $healthChange]);

    // Жир
    $stmt = $db->prepare("
        INSERT INTO user_stats (user_id, stat_code, value)
        VALUES (?, 'fat', ?)
        ON DUPLICATE KEY UPDATE
        value = LEAST(100, GREATEST(0, value + VALUES(value)))
    ");
    $stmt->execute([$userId, $fatChange]);

    // Физическая форма (реакция на жир)
    if ($physicalChange !== 0) {
        $stmt = $db->prepare("
            INSERT INTO user_stats (user_id, stat_code, value)
            VALUES (?, 'physical', ?)
            ON DUPLICATE KEY UPDATE
            value = LEAST(100, GREATEST(0, value + VALUES(value)))
        ");
        $stmt->execute([$userId, $physicalChange]);
    }

$db->commit();

$achievement = new AchievementService($db, $userId);
$achievement->checkAll();



    // Фидбек пользователю
    $_SESSION['action_feedback'] = [
        ['label' => 'Здоровье', 'value' => $healthChange],
        ['label' => 'Жир', 'value' => $fatChange],
        ['label' => 'Физическая форма', 'value' => $physicalChange],
        ['label' => 'Опыт', 'value' => $expChange],
    ];

    header('Location: /index.php');
    exit;

} catch (Throwable $e) {
    $db->rollBack();
    echo "<pre>Ошибка: {$e->getMessage()}</pre>";
    exit;
}
