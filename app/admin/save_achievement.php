<?php
session_start();
require '../functions/db.php';
$db->query("
INSERT INTO achievements (code,title,description,icon)
VALUES (
    '{$_POST['code']}',
    '{$_POST['title']}',
    '{$_POST['description']}',
    '{$_POST['icon']}'
)
");

$achievementId = $conn->insert_id;

$conn->query("
INSERT INTO achievement_conditions
(achievement_id, condition_type, condition_value)
VALUES (
    $achievementId,
    '{$_POST['condition_type']}',
    '{$_POST['condition_value']}'
)
");
header("Location: achievements.php");
?>