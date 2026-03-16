<?php
session_start();
require '../functions/db.php';
$stmt = $db->prepare("
INSERT INTO achievements (code,title,description,icon)
VALUES (?,?,?,?)
");
$stmt->execute([
$_POST['code'],
$_POST['title'],
$_POST['description'],
$_POST['icon']
]);

$achievementId = $db->lastInsertId();

$stmt = $db->prepare("
INSERT INTO achievement_conditions
(achievement_id, condition_type, condition_value)
VALUES (?,?,?)
");

$stmt->execute([
$achievementId,
$_POST['condition_type'],
$_POST['condition_value']
]);

header("Location: achievements.php");
?>