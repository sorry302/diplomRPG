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

$userId = (int)$userId;
$activityId = (int)($_POST['activity_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));

// Получаем активность
$result = mysqli_query($conn, "
    SELECT *
    FROM activities
    WHERE id = $activityId AND category = 'intellectual'
");

$activity = mysqli_fetch_assoc($result);

if (!$activity) {
    header('Location: /index.php');
    exit;
}

// Расчёт эффектов
$fatChange         = $activity['obesity_change'] * $quantity;
$expChange         = $activity['exp_change'] * $quantity;
$intellectualChange = $activity['intellectual_change'] * $quantity;

mysqli_begin_transaction($conn);

try {

    // Лог активности
    mysqli_query($conn, "
        INSERT INTO activity_logs (user_id, activity_id, quantity)
        VALUES ($userId, $activityId, $quantity)
    ");

    // Жир
    mysqli_query($conn, "
        UPDATE user_stats
        SET value = LEAST(100, GREATEST(0, value + $fatChange))
        WHERE user_id = $userId AND stat_code = 'fat'
    ");

    // Интеллект
    mysqli_query($conn, "
        UPDATE user_stats
        SET value = LEAST(100, GREATEST(0, value + $intellectualChange))
        WHERE user_id = $userId AND stat_code = 'intellect'
    ");

    // Опыт
    $expService = new ExperienceService($conn);
    $expService->addExp($userId, $expChange);

    mysqli_commit($conn);

    // Достижения
    $achievement = new AchievementService($conn, $userId);
    $achievement->checkAll();

    $_SESSION['action_feedback'] = [
        ['label' => 'Жир', 'value' => $fatChange],
        ['label' => 'Интеллект', 'value' => $intellectualChange],
        ['label' => 'Опыт', 'value' => $expChange],
    ];

} catch (Throwable $e) {
    mysqli_rollback($conn);
    echo "Ошибка: " . $e->getMessage();
    exit;
}

header('Location: /index.php');
exit;