<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';
?>

<main>
    <div class="panel">

        <h3 style="margin-top: 0; margin-bottom: 15px; color: var(--accent-gold);">
            Создать новое достижение
        </h3>

        <form method="POST" action="save_achievement.php" style="display: flex; flex-direction: column; gap: 12px;">

            <div class="form-group">
                <label>Код (уникальный ID)</label>
                <input name="code" placeholder="например: first_step" required>
            </div>

            <div class="form-group">
                <label>Название</label>
                <input name="title" placeholder="Первый шаг" required>
            </div>

            <div class="form-group">
                <label>Описание</label>
                <input name="description" placeholder="Вы сделали первое действие" required>
            </div>

            <div class="form-group">
                <label>Иконка (Emoji)</label>
                <input name="icon" placeholder="🏆" required>
            </div>

            <div class="form-group">
                <label>Тип условия</label>
                <select name="condition_type">
                    <option value="eat_count">Съесть X раз (порции)</option>
                    <option value="activity_count">Сделать X действий (всего)</option>
                    <option value="level_reach">Достичь уровня</option>
                    <option value="exp">Набрать опыта</option>
                    <option value="stat_physical">Физическая форма ≥ X</option>
                    <option value="stat_intellect">Интеллект ≥ X</option>
                    <option value="stat_spiritual">Духовность ≥ X</option>
                </select>
            </div>

            <div class="form-group">
                <label>Значение условия</label>
                <input type="number" name="condition_value" placeholder="10" required>
            </div>

            <button type="submit" class="btn-primary">
                Создать достижение
            </button>

        </form>

    </div>
</main>

<style>
.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 10px;
}

.form-group label {
    font-size: 12px;
    color: var(--text-muted);
    margin-left: 4px;
}

input, select {
    background: #151821;
    border: 1px solid rgba(255,255,255,0.06);
    color: #e5e7eb;
    border-radius: 12px;
    padding: 11px 12px;
    font-size: 14px;
}

.btn-primary {
    background: var(--accent-positive);
    color: #000;
    font-weight: bold;
    border: none;
    padding: 12px;
    border-radius: 12px;
    cursor: pointer;
    margin-top: 10px;
}

.btn-primary:hover {
    opacity: 0.9;
}
</style>