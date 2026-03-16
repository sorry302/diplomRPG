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

$activityId = (int)($_POST['activity_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));

/* ===== Получаем активность ===== */

$stmt = $db->prepare("
    SELECT *
    FROM activities
    WHERE id = ? AND category = 'physical'
");

$stmt->execute([$activityId]);

$activity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activity) {
    header('Location: /index.php');
    exit;
}

/* ===== Рассчитываем изменения ===== */

$healthChange   = $activity['health_change'] * $quantity;
$physicalChange = $activity['physical_change'] * $quantity;
$fatChange      = $activity['obesity_change'] * $quantity;
$expChange      = $activity['exp_change'] * $quantity;

try {

    $db->beginTransaction();

    /* ===== XP ===== */

    $expService = new ExperienceService($db);
    $expService->addExp($userId, $expChange);

    /* ===== Лог активности ===== */

    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, activity_id, quantity)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$userId, $activityId, $quantity]);

    /* ===== Обновление статов ===== */

    function updateStat(PDO $db, int $userId, string $code, int $change)
    {
        if ($change == 0) return;

        $stmt = $db->prepare("
            INSERT INTO user_stats (user_id, stat_code, value)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            value = LEAST(100, GREATEST(0, value + VALUES(value)))
        ");

        $stmt->execute([$userId, $code, $change]);
    }

    updateStat($db, $userId, 'health', $healthChange);
    updateStat($db, $userId, 'physical', $physicalChange);
    updateStat($db, $userId, 'fat', $fatChange);

    $db->commit();


    $achievement = new AchievementService($db, $userId);
$achievement->checkAll();



    /* ===== Feedback ===== */

    $_SESSION['action_feedback'] = [
        ['label' => 'Здоровье', 'value' => $healthChange],
        ['label' => 'Физическая форма', 'value' => $physicalChange],
        ['label' => 'Жир', 'value' => $fatChange],
        ['label' => 'Опыт', 'value' => $expChange],
    ];

    header('Location: /index.php');
    exit;

}
catch (Throwable $e)
{
    $db->rollBack();
    echo $e->getMessage();
}
