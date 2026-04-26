<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';

if(isset($_GET['id'])){
    $conn->query("DELETE FROM achievements WHERE `id` = '{$_GET['id']}'");
    header("Location: achievements.php");
}
?>

<style>
    .admin-container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .panel {
        background: rgba(30, 30, 35, 0.9);
        border: 1px solid #444;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        margin-bottom: 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 13px;
        color: var(--accent-gold, #ffd700);
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    input, select, textarea {
        background: #2a2a2e;
        border: 1px solid #555;
        color: #fff;
        padding: 12px;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    input:focus, select:focus {
        border-color: var(--accent-gold, #ffd700);
        outline: none;
    }

    .btn-primary {
        background: var(--accent-positive, #28a745);
        color: #fff;
        font-weight: bold;
        border: none;
        padding: 15px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
        font-size: 16px;
        transition: filter 0.3s;
        grid-column: 1 / -1;
    }

    .btn-primary:hover {
        filter: brightness(1.2);
    }

    /* Стили таблицы */
    .table-responsive {
        overflow-x: auto;
    }

    .rpg-table {
        width: 100%;
        border-collapse: collapse;
        color: #e0e0e0;
        font-size: 14px;
    }

    .rpg-table th {
        background: rgba(0, 0, 0, 0.3);
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #444;
        color: var(--accent-gold, #ffd700);
        text-transform: uppercase;
        font-size: 12px;
    }

    .rpg-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #333;
        vertical-align: middle;
    }

    .rpg-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .icon-cell {
        font-size: 24px;
        text-align: center;
        width: 50px;
    }

    .actions {
        display: flex;
        gap: 10px;
    }

    .btn-delete {
        color: #dc3545;
        text-decoration: none;
        font-size: 18px;
        transition: transform 0.2s;
    }

    .btn-delete:hover {
        transform: scale(1.3);
    }

    /* Адаптивность таблицы */
    @media (max-width: 768px) {
        .rpg-table thead {
            display: none; /* Скрываем заголовки на мобилках */
        }

        .rpg-table, .rpg-table tbody, .rpg-table tr, .rpg-table td {
            display: block;
            width: 100%;
        }

        .rpg-table tr {
            margin-bottom: 15px;
            border: 1px solid #444;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            padding: 10px;
        }

        .rpg-table td {
            text-align: right;
            padding: 8px 10px;
            border-bottom: 1px solid #333;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rpg-table td:last-child {
            border-bottom: none;
        }

        .rpg-table td::before {
            content: attr(data-label);
            font-weight: bold;
            color: var(--accent-gold);
            text-transform: uppercase;
            font-size: 11px;
            margin-right: 15px;
        }

        .icon-cell {
            width: 100% !important;
            font-size: 32px;
            justify-content: center !important;
            background: rgba(0,0,0,0.2);
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .actions {
            justify-content: flex-end;
        }

        .title-wrapper {
            text-align: right;
        }
    }
</style><main class="admin-container">
    <div class="panel">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--accent-gold);">
            🏆 Создать новое достижение
        </h3>

        <form method="POST" action="save_achievement.php" class="form-grid">
            <div class="form-group">
                <label>Код (уникальный ID)</label>
                <input name="code" placeholder="например: first_step" required>
            </div>

            <div class="form-group">
                <label>Название</label>
                <input name="title" placeholder="Первый шаг" required>
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

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Описание</label>
                <input name="description" placeholder="Вы сделали первое действие" required>
            </div>

            <button type="submit" class="btn-primary">
                Создать достижение
            </button>
        </form>
    </div>

    <div class="panel">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: var(--accent-gold);">
            📜 Список достижений
        </h3>

        <div class="table-responsive">
            <table class="rpg-table">
                <thead>
                    <tr>
                        <th>Иконка</th>
                        <th>Код / Название</th>
                        <th>Описание</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM achievements ORDER BY id DESC");
                    while($ach = mysqli_fetch_assoc($res)):
                    ?>
                    <tr>
                        <td class="icon-cell" data-label="Иконка"><?= htmlspecialchars($ach['icon']) ?></td>
                        <td data-label="Название">
                            <div class="title-wrapper">
                                <div style="font-weight: bold; color: #fff;"><?= htmlspecialchars($ach['title']) ?></div>
                                <div style="font-size: 11px; color: #888;"><?= htmlspecialchars($ach['code']) ?></div>
                            </div>
                        </td>                       
                        <td data-label="Описание" style="font-size: 13px; color: #ccc;"><?= htmlspecialchars($ach['description']) ?></td>
                        <td class="actions" data-label="Действия">
                            <a href="edit_achievement.php?id=<?= (int)$ach['id'] ?>" class="btn-delete">✏️</a>
                            <a href="achievements.php?id=<?= (int)$ach['id'] ?>" class="btn-delete" onclick="return confirm('Удалить достижение?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>            
            </table>
        </div>
    </div>
</main>