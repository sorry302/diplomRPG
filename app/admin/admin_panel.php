<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>

<main class="admin-container">
    <div class="panel">
        <h2 style="margin-top: 0; color: var(--accent-gold); text-align: center; margin-bottom: 25px;">🛡️ Панель управления RPG</h2>
        
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
                    <p>Управление списком еды и их эффектами на статы</p>
                </div>
            </a>

            <a href="activ_stats.php" class="admin-card">
                <div class="card-icon">⚡</div>
                <div class="card-info">
                    <h4>Активности</h4>
                    <p>Настройка тренировок, обучения и привычек</p>
                </div>
            </a>

            <a href="user_list.php" class="admin-card">
                <div class="card-icon">👥</div>
                <div class="card-info">
                    <h4>Пользователи</h4>
                    <p>Просмотр статистики и редактирование параметров игроков</p>
                </div>
            </a>
        </div>
    </div>
</main>

<style>
.admin-container {
    max-width: 800px;
    margin: 20px auto;
    padding: 0 15px;
}

.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 15px;
}

.admin-card {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--border-soft);
    padding: 15px;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.admin-card:hover {
    background: rgba(255, 255, 255, 0.07);
    border-color: var(--accent-gold);
    transform: translateY(-2px);
}

.card-icon {
    font-size: 32px;
    background: rgba(0, 0, 0, 0.2);
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.card-info h4 {
    margin: 0 0 5px 0;
    color: #fff;
}

.card-info p {
    margin: 0;
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.3;
}
</style>
