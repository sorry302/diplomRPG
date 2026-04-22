<?php
session_start();
require 'app/functions/auth_check.php';
require_once __DIR__ . '/config/config.php';
require 'app/components/header.php';
$actionFeedback = $_SESSION['action_feedback'] ?? null;
unset($_SESSION['action_feedback']);


/* ПОЛЬЗОВАТЕЛЬ*/
$query = "
    SELECT u.username, u.created_at,
           e.level, e.exp
    FROM users u
    LEFT JOIN experience e ON e.user_id = u.id
    WHERE u.id = $userId
";
$user = $conn->query($query)->fetch_assoc();

/* СТАТЫ */
$query = "
    SELECT stat_code, value
    FROM user_stats
    WHERE user_id = $userId
";
$result = $conn->query($query);

$stats = [];
while ($row = $result->fetch_assoc()) {
    $stats[$row['stat_code']] = $row['value'];
}

/*  Достижение  */
$query = "
    SELECT a.title, a.description, a.icon, ua.achieved_at
    FROM user_achievements ua
    JOIN achievements a ON a.id = ua.achievement_id
    WHERE ua.user_id = $userId
    ORDER BY ua.achieved_at DESC
";
$achievements = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

/* АКТИВНОСТЬ*/
$query = "
    (
        SELECT 
            'food' AS type,
            f.name AS title,
            fl.portions AS amount,
            fl.created_at
        FROM food_logs fl
        JOIN foods f ON f.id = fl.food_id
        WHERE fl.user_id = $userId
    )
    UNION ALL
    (
        SELECT
            'activity' AS type,
            a.name AS title,
            al.quantity AS amount,
            al.created_at
        FROM activity_logs al
        JOIN activities a ON a.id = al.activity_id
        WHERE al.user_id = $userId
    )
    ORDER BY created_at DESC
    LIMIT 10
";
$activities = $conn->query($query)->fetch_all(MYSQLI_ASSOC);


$query = "
    SELECT 
        u.username,
        u.created_at,
        e.level,
        e.exp,
        up.avatar
    FROM users u
    LEFT JOIN experience e ON e.user_id = u.id
    LEFT JOIN user_profile up ON up.user_id = u.id
    WHERE u.id = $userId
";
$userAva = $conn->query($query)->fetch_assoc();


/* ===== XP ПРОГРЕСС ===== */
$nextLevel = $user['level'] + 1;
$query = "SELECT required_exp FROM levels WHERE level = $nextLevel";
$res = $conn->query($query);
$nextLevelExp = $res->fetch_row()[0] ?? null;if (!$nextLevelExp) {
    $xpPercent = 100; // максимальный уровень
    $nextLevelExp = $user['exp'];
} else {
    $xpPercent = min(100, ($user['exp'] / $nextLevelExp) * 100);
}
?>  
<link rel="stylesheet" href="/assets/css/profile.css">
<main class="profile">

<!-- ===== ОСНОВНОЕ ===== -->
 <section class="card left-top">

<?php
$avatarFile = $userAva['avatar'] ?? 'default.png';

// защита на случай пустого значения
if ($avatarFile === '' || $avatarFile === null) {
    $avatarFile = 'default.png';
}

$avatarUrl = '/uploads/avatars/' . basename($avatarFile);
?>

<img
    src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
    class="avatar"
    alt="Аватар пользователя"
>

    <div>
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <a href="/profile_edit.php" class="btn-edit">✏️ Редактировать профиль</a>
        <p>Уровень: <strong><?= $user['level'] ?></strong></p>

        <div class="xp-bar">
            <div class="xp-fill" style="width: <?= $xpPercent ?>%"></div>
        </div>
        <small><?= $user['exp'] ?> / <?= $nextLevelExp ?> XP</small>
    </div>
</section>

<!-- ===== ХАРАКТЕРИСТИКИ ===== -->
<section class="card profile-main right-top">
    <h3>Характеристики</h3>

    <?php
    $statMeta = [
        'health'     => ['Здоровье', '#4caf50'],
        'energy'     => ['Энергия', '#03a9f4'],
        'physical'   => ['Физика', '#ff9800'],
        'intellect'  => ['Интеллект', '#9c27b0'],
        'spiritual'  => ['Духовность', '#607d8b'],
        'fat'        => ['Форма тела', '#f44336'],
    ];

    foreach ($statMeta as $code => [$label, $color]):
        $value = $stats[$code] ?? 0;
    ?>
        <div class="stat">
            <div class="stat-label"><?= $label ?></div>
            <div class="bar">
                <div class="fill" style="width: <?= $value ?>%; background: <?= $color ?>"></div>
            </div>
            <span><?= $value ?>%</span>
        </div>
    <?php endforeach; ?>
</section>

<section class="card profile-main">
    <h3>Достижения</h3>

    <?php if (!$achievements): ?>
        <p class="muted">Пока нет достижений</p>
    <?php endif; ?>

    <ul class="achievements">
        <?php foreach ($achievements as $a): ?>
            <li>
                <span class="icon"><?= $a['icon'] ?></span>
                <div>
                    <strong><?= htmlspecialchars($a['title']) ?></strong>
                    <small><?= htmlspecialchars($a['description']) ?></small>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>


<!-- ===== АКТИВНОСТЬ ===== -->
<section class="card .right-middle">
    <h3>Последние действия</h3>

    <?php if (!$activities): ?>
        <p class="muted">Пока нет активности</p>
    <?php endif; ?>

    <ul class="activity-list">
        <?php foreach ($activities as $act): ?>
            <li>
                <?php if ($act['type'] === 'food'): ?>
                    🍽
                <?php else: ?>
                    🏃
                <?php endif; ?>
                <?= htmlspecialchars($act['title']) ?>
                ×<?= $act['amount'] ?>

                <small><?= date('d.m H:i', strtotime($act['created_at'])) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

</main>
</body>
</html>
