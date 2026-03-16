<?php
session_start();
require_once __DIR__ . '/db.php';

$userId = $_SESSION['user']['user_id'] ?? null;

if (!$userId) {
    header('Location: /index.php');
    exit;
}

/* ===== НИКНЕЙМ ===== */
$username = trim($_POST['username'] ?? '');

if ($username === '') {
    header('Location: ../../profile_edit.php?error=username');
    exit;
}

/* ===== АВАТАР ===== */
$avatarName = null;

if (!empty($_FILES['avatar']['name'])) {
    $file = $_FILES['avatar'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Ошибка загрузки файла');
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        die('Файл слишком большой (макс. 2MB)');
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        die('Недопустимый формат файла');
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $avatarName = 'avatar_' . $userId . '.' . $extension;

   $uploadDir = __DIR__ . '/../../uploads/avatars/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadPath = $uploadDir . $avatarName;

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    die('Не удалось сохранить файл');
}
}

/* ===== ОБНОВЛЕНИЕ USERS ===== */
$stmt = $db->prepare("
    UPDATE users
    SET username = ?
    WHERE id = ?
");
$stmt->execute([$username, $userId]);

/* ===== ОБНОВЛЕНИЕ USER_PROFILE ===== */
if ($avatarName) {
    $stmt = $db->prepare("
        UPDATE user_profile
        SET avatar = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$avatarName, $userId]);
}

/* ===== СЕССИЯ ===== */
$_SESSION['user']['username'] = $username;

header('Location: ../../profile.php');
exit;
