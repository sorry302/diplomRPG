<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>


<main>
    <div class="panel">
<main>
    <div class="panel">
        <h3 style="margin-top: 0; margin-bottom: 15px; color: var(--accent-gold);">Создать новое достижение</h3>
        <form method="POST" action="save_achievement.php" style="display: flex; flex-direction: column; gap: 12px;">
            
            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Код (уникальный ID)</label>
                <input name="code" placeholder="например: first_step" required style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Название</label>
                <input name="title" placeholder="Первый шаг" required style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Описание</label>
                <input name="description" placeholder="Вы сделали первое действие" required style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Иконка (Emoji)</label>
                <input name="icon" placeholder="🏆" required style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Тип условия</label>
                <select name="condition_type" style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
                    <option value="eat_count">Съесть X раз (порции)</option>
                    <option value="activity_count">Сделать X действий (всего)</option>
                    <option value="level_reach">Достичь уровня</option>
                    <option value="exp">Набрать опыта</option>
                    <option value="stat_physical">Физическая форма >= X</option>
                    <option value="stat_intellect">Интеллект >= X</option>
                    <option value="stat_spiritual">Духовность >= X</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Значение условия</label>
                <input type="number" name="condition_value" placeholder="10" required style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;">
            </div>

            <button type="submit" style="background: var(--accent-positive); color: #000; border: none; padding: 12px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 10px;">
                Создать достижение
            </button>
        </form>
    </div>
</main>
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
input {
    background: #151821;
    border: 1px solid rgba(255,255,255,0.06);
    color: #e5e7eb;
    border-radius: 12px;
    padding: 11px 12px;
    font-size: 14px;
}
</style>
