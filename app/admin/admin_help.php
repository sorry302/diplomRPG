<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';


$help_list = $conn -> query("SELECT * FROM `help`");
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
    }

    .add-post-form {
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 15px; 
        margin-top: 15px;
    }

    .add-post-form input, .add-post-form textarea, .add-post-form select {
        background: #2a2a2e;
        border: 1px solid #555;
        color: #fff;
        padding: 12px;
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
    }

    .checkbox-group {
        display: flex;
        gap: 20px;
        align-items: center;
        color: #ccc;
    }

    .btn-add {
        background: var(--accent-positive, #28a745);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-add:hover {
        filter: brightness(1.2);
    }

    .table-responsive {
        overflow-x: auto;
        margin-top: 20px;
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
        padding: 12px 10px;
        border-bottom: 2px solid #444;
        color: var(--accent-gold, #ffd700);
        text-transform: uppercase;
        font-size: 12px;
    }

    .rpg-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #333;
        vertical-align: middle;
    }

    .rpg-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .actions {
        display: flex;
        gap: 12px;
    }

    .btn-edit, .btn-delete {
        text-decoration: none;
        transition: transform 0.2s;
        display: inline-block;
    }

    .btn-edit:hover, .btn-delete:hover {
        transform: scale(1.3);
    }

    summary {
        padding: 12px;
        background: rgba(255,255,255,0.05);
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        color: var(--accent-positive, #28a745);
        transition: background 0.3s;
    }

    summary:hover {
        background: rgba(255,255,255,0.1);
    }

    .badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: bold;
    }
    .badge-active { background: #28a745; color: #fff; }
    .badge-inactive { background: #555; color: #ccc; }
    .badge-pinned { background: #ffd700; color: #000; }

    /* Адаптивность */
    @media (max-width: 768px) {
        .admin-container {
            padding: 10px;
        }

        .panel {
            padding: 15px;
        }

        .add-post-form div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important; /* Селект и чекбоксы в одну колонку */
        }

        .checkbox-group {
            justify-content: space-around;
            background: rgba(255,255,255,0.05);
            padding: 10px;
            border-radius: 4px;
        }

        .rpg-table thead {
            display: none;
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
            font-size: 14px;
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

        .actions {
            justify-content: flex-end;
        }
    }
</style>
<main class="admin-container">
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold);">
            📝 Управление помощью
        </h3>
        <details style="margin-bottom: 30px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary>+ Создать новый пост</summary>
            <form method="POST" action="save_help.php" class="add-post-form">
                <input type="text" name="title" placeholder="Заголовок помощи" required>
                <textarea name="content" placeholder="Текст помощи..." rows="6" required></textarea>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <select name="category">
                        <option value="guide">Инструкция</option>
                        <option value="faq">Вопросы/ответы</option>
                        <option value="tips">Советы</option>
                    </select>
                </div>

                <button type="submit" name="add_help" class="btn-add">Опубликовать помощь</button>
            </form>
        </details>

    <div class="table-responsive">
            <table class="rpg-table">
                <thead>
                    <tr>
                        <th>Заголовок</th>
                        <th>Тип</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($help_list)): ?>
                    <tr>
                        <td data-label="Заголовок" style="font-weight: bold; color: #fff;">
                            <?= htmlspecialchars($row['title']) ?>
                        </td>
                        <td data-label="Тип">
                            <span style="color: #aaa; font-size: 12px;"><?= strtoupper($row['category']) ?></span>
                        </td>
                        <td data-label="Статус">
                            <div>
                                <?php if($row['is_active'] == 1): ?>
                                    <span class="badge badge-active">Активен</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Скрыт</span>
                                <?php endif; ?>
                                
                            </div>
                        </td>
                        <td data-label="Дата" style="color: #888; font-size: 12px;">
                            <?= date('d.m.Y H:i', strtotime($row['created_at'])) ?>
                        </td>
                        <td data-label="Действия" class="actions">
                            <a href="edit_help.php?id=<?= $row['id'] ?>" class="btn-edit" title="Редактировать">✏️</a>
                            <?php 
                            if($row['is_active'] == 2){
                            ?>
                            <a href="action_help.php?id=<?= $row['id'] ?>" class="btn-edit" title="Показать">👁️</a>
                            <?php } ?>

                            <?php 
                            if($row['is_active'] == 1){
                            ?>
                            <a href="action_help.php?id=<?= $row['id'] ?>" class="btn-edit" title="Скрыть">🫣</a>
                            <?php } ?>
                            <a href="action_help.php?delete=<?= $row['id'] ?>" class="btn-delete" title="Удалить" onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>