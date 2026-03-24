<?php
require_once __DIR__ . '/db.php';

$userId = $_SESSION['user']['user_id'] ?? null;

$stats = [];

if ($userId) {

    $userId = (int)$userId;

    $result = mysqli_query($conn, "
        SELECT stat_code, value
        FROM user_stats
        WHERE user_id = $userId
    ");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $stats[$row['stat_code']] = (int)$row['value'];
        }
    }
}