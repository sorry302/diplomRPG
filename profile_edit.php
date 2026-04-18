<?php
session_start();
require 'app/functions/auth_check.php';
require_once __DIR__ . '/config/config.php';
require 'app/components/header.php';

$userId = $_SESSION['user']['user_id'];

/* ===== ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ===== */
$query = "
    SELECT 
        u.username,
        up.avatar
    FROM users u
    LEFT JOIN user_profile up ON up.user_id = u.id
    WHERE u.id = $userId
";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$avatarFile = $user['avatar'] ?? 'default.png';?>

<link rel="stylesheet" href="/assets/css/profile_edit.css">

<main class="profile-edit">

    <h2>Редактирование профиля</h2>

    <form
        action="/app/functions/update_profile.php"
        method="post"
        enctype="multipart/form-data"
        class="card"
    >

        <!-- АВАТАР -->
        <div class="avatar-block">
            <img
                src="/uploads/avatars/<?= htmlspecialchars($avatarFile) ?>"
                class="avatar-preview"
                alt="Аватар"
            >
        </div>

        <!-- НИКНЕЙМ -->
        <label>
            Никнейм
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </label>

        <!-- ЗАГРУЗКА АВАТАРА -->
        <label>
            Новый аватар
            <input type="file" name="avatar" accept="image/*">
        </label>

        <button type="submit">Сохранить изменения</button>

    </form>

</main>

</body>
</html>
