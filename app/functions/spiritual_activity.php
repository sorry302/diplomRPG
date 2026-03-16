<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../services/ExperienceService.php';
require_once __DIR__ . '/../services/AchievementService.php';

$userId = $_SESSION['user']['user_id'] ?? null;
if (!$userId) {
    header('Location: /index.php');
    exit;
}

$activityId = (int)($_POST['activity_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));//получаем количество повторений

// Получаем активность ТОЛЬКО spiritual
$stmt = $db->prepare("
    SELECT *
    FROM activities
    WHERE id = ? AND category = 'spiritual'
");
$stmt->execute([$activityId]);
$activity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activity) {
    header('Location: /index.php');
    exit;
}

// Расчёт эффектов
$expChange    = $activity['exp_change'] * $quantity;
$spiritualChange    = $activity['spiritual_change'] * $quantity;
$intellectualChange    = $activity['intellectual_change'] * $quantity;

$db->beginTransaction();

$expService = new ExperienceService($db);
$expService->addExp($userId, $activity['exp_change'] * $portions);

try {
    // Лог активности
    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, activity_id, quantity)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $activityId, $quantity]);

    // Жир
    $stmt = $db->prepare("
        UPDATE user_stats
        SET value = LEAST(100, GREATEST(0, value + ?))
        WHERE user_id = ? AND stat_code = 'spiritual'
    ");
    $stmt->execute([$spiritualChange, $userId]);

    // интеллект
    $stmt = $db->prepare("
        UPDATE user_stats
        SET value = LEAST(100, GREATEST(0, value + ?))
        WHERE user_id = ? AND stat_code = 'intellect'
    ");
    $stmt->execute([$intellectualChange, $userId]);

    // Опыт
    $stmt = $db->prepare("
        INSERT INTO experience (user_id, exp)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE exp = exp + VALUES(exp)
    ");
    $stmt->execute([$userId, $expChange]);

    $db->commit();

    $achievement = new AchievementService($db, $userId);
$achievement->checkAll();


    
     $_SESSION['action_feedback'] = [
    ['label' => 'Здоровье', 'value' => $healthChange],
    ['label' => 'Духовность', 'value' => $spiritualChange],
    ['label' => 'Интеллект', 'value' => $intellectualChange],
    ['label' => 'Опыт', 'value' => $expChange],
];

} catch (Throwable $e) {
    $db->rollBack();
    throw $e; //Если любая ошибка откатываем все изменения, как будто ничего не было.
}

header('Location: /index.php');
exit;
