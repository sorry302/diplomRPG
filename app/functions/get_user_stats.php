<?php
require_once __DIR__ . '/db.php';

$userId = $_SESSION['user']['user_id'] ?? null;

$stats = [];

if ($userId) {
    $stmt = $db->prepare("
        SELECT stat_code, value
        FROM user_stats
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $stats[$row['stat_code']] = (int)$row['value'];
    }
}
