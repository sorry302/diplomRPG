<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';
?>

<main class="admin-container">
    <div class="panel">

        <h2 style="margin-top: 0; color: var(--accent-gold); text-align: center; margin-bottom: 25px;">
            🛡️ Панель управления RPG
        </h2>

        <div class="admin-grid">

            <a href="achievements.php" class="admin-card">
                <div class="card-icon">🏆</div>
                <div class="card-info">
                    <h4>Достижения</h4>
                    <p>Создание и редактирование условий получения наград</p>
                </div>
            </a>

            <a href="eat_state.php" class="admin-card">
                <div class="card-icon">🍎</div>
                <div class="card-info">
                    <h4>Еда и продукты</h4>
                    <p>Управление списком еды и их эффектами</p>
                </div>
            </a>

            <a href="activ_stats.php" class="admin-card">
                <div class="card-icon">⚡</div>
                <div class="card-info">
                    <h4>Активности</h4>
                    <p>Настройка тренировок и привычек</p>
                </div>
            </a>

            <a href="user_list.php" class="admin-card">
                <div class="card-icon">👥</div>
                <div class="card-info">
                    <h4>Пользователи</h4>
                    <p>Просмотр и редактирование игроков</p>
                </div>
            </a>

            <a href="post.php" class="admin-card">
                <div class="card-icon">🖋️</div>
                <div class="card-info">
                    <h4>Посты</h4>
                    <p>Отправка постов</p>
                </div>
            </a>

        </div>

    </div>
</main>