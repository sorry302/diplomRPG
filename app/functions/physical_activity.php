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

$userId = (int)$userId;
$activityId = (int)($_POST['activity_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));

/* ===== Получаем активность ===== */

$result = mysqli_query($conn, "
    SELECT *
    FROM activities
    WHERE id = $activityId AND category = 'physical'
");

$activity = mysqli_fetch_assoc($result);

if (!$activity) {
    header('Location: /index.php');
    exit;
}

/* ===== Рассчитываем изменения ===== */

$healthChange   = $activity['health_change'] * $quantity;
$physicalChange = $activity['physical_change'] * $quantity;
$fatChange      = $activity['obesity_change'] * $quantity;
$expChange      = $activity['exp_change'] * $quantity;

mysqli_begin_transaction($conn);

try {

    /* ===== XP ===== */

    $expService = new ExperienceService($conn);
    $expService->addExp($userId, $expChange);

    /* ===== Лог активности ===== */

    mysqli_query($conn, "
        INSERT INTO activity_logs (user_id, activity_id, quantity)
        VALUES ($userId, $activityId, $quantity)
    ");

    /* ===== Обновление статов ===== */

    function updateStat($conn, int $userId, string $code, int $change)
    {
        if ($change == 0) return;

        $code = mysqli_real_escape_string($conn, $code);

        mysqli_query($conn, "
            INSERT INTO user_stats (user_id, stat_code, value)
            VALUES ($userId, '$code', $change)
            ON DUPLICATE KEY UPDATE
            value = LEAST(100, GREATEST(0, value + VALUES(value)))
        ");
    }

    updateStat($conn, $userId, 'health', $healthChange);
    updateStat($conn, $userId, 'physical', $physicalChange);
    updateStat($conn, $userId, 'fat', $fatChange);

    mysqli_commit($conn);

    /* ===== Достижения ===== */

    $achievement = new AchievementService($conn, $userId);
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
    mysqli_rollback($conn);
    echo $e->getMessage();
}