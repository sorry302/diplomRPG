<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';

if(isset($_GET['id'])){
    $conn->query("DELETE FROM activities WHERE `id` = '{$_GET['id']}'");
    header("Location: activ_stats.php");
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
    }

    /* Стили формы */
    .add-activity-form {
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 15px; 
        margin-top: 15px;
    }

    .add-activity-form input, .add-activity-form select {
        background: #2a2a2e;
        border: 1px solid #555;
        color: #fff;
        padding: 10px;
        border-radius: 4px;
    }

    .btn-add {
        background: var(--accent-positive, #28a745);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        grid-column: 1 / -1;
    }

    .btn-add:hover {
        filter: brightness(1.2);
    }

    /* Стили таблицы */
    .table-responsive {
        overflow-x: auto;
        margin-top: 10px;
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
        padding: 10px;
        border-bottom: 1px solid #333;
        vertical-align: middle;
    }

    .rpg-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    /* Цвета для статов */
    .pos { color: #28a745; font-weight: bold; }
    .neg { color: #dc3545; font-weight: bold; }

    .actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit, .btn-delete {
        text-decoration: none;
        padding: 5px;
        transition: transform 0.2s;
        display: inline-block;
    }

    .btn-edit:hover, .btn-delete:hover {
        transform: scale(1.3);
    }

    summary {
        padding: 10px;
        background: rgba(255,255,255,0.05);
        border-radius: 5px;
        transition: background 0.3s;
    }

    summary:hover {
        background: rgba(255,255,255,0.1);
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .admin-container {
            padding: 10px;
        }

        .panel {
            padding: 15px;
        }

        .add-activity-form {
            grid-template-columns: 1fr;
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
            ⚡ Управление активностями
        </h3>

        <!-- ===== ДОБАВЛЕНИЕ ===== -->
        <details style="margin-bottom: 30px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive, #28a745);">
                + Добавить новую активность
            </summary>

            <form method="POST" action="save_activity.php" class="add-activity-form">    

                <input name="name" placeholder="Название (например: Бег)" required>    

                <select name="category">
                    <option value="physical">Физическая</option>
                    <option value="intellectual">Интеллектуальная</option>
                    <option value="spiritual">Духовная</option>
                    <option value="bad_habit">Вредная привычка</option>
                </select>

                <input type="number" name="health_change" placeholder="❤️ HP (+/-)" required>
                <input type="number" name="physical_change" placeholder="💪 Физ. форма (+/-)" required>
                <input type="number" name="intellectual_change" placeholder="🧠 Интеллект (+/-)" required>
                <input type="number" name="spiritual_change" placeholder="🧘 Духовность (+/-)" required>
                <input type="number" name="obesity_change" placeholder="🍔 Жир (+/-)" required>      
                <input type="number" name="exp_change" placeholder="🔥 Опыт (+)" required>

                <button type="submit" name="save_activ" class="btn-add">Сохранить активность</button>    
            </form>
        </details>

        <?php
        $categories = [
            'physical' => '💪 Физическая активность',
            'intellectual' => '🧠 Интеллектуальная',
            'spiritual' => '🧘 Духовная',
            'bad_habit' => '🚬 Вредные привычки'
        ];

        foreach ($categories as $catKey => $catName):
 //делаем безопасный запрос в стринг
            $catKeySafe = mysqli_real_escape_string($conn, $catKey);

            $result = mysqli_query($conn, "
                SELECT * FROM activities 
                WHERE category = '$catKeySafe' 
                ORDER BY id DESC
            ");

            if (!$result || mysqli_num_rows($result) === 0) continue;
        ?>

        <h4 style="color: var(--text-muted); margin-bottom: 10px; border-left: 3px solid var(--accent-gold); padding-left: 10px;">
            <?= $catName ?>
        </h4>

        <div class="table-responsive" style="margin-bottom: 30px;">
            <table class="rpg-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>❤️ HP</th>
                        <th>💪 Физ</th>
                        <th>🧠 Инт</th>
                        <th>🧘 Дух</th>
                        <th>🍔 Жир</th>
                        <th>🔥 XP</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($act = mysqli_fetch_assoc($result)): ?>

                    <tr>
                        <td data-label="Название" style="font-weight: bold;">
                            <?= htmlspecialchars($act['name']) ?>
                        </td>

                        <td data-label="❤️ HP" class="<?= $act['health_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['health_change'] ?>
                        </td>

                        <td data-label="💪 Физ" class="<?= $act['physical_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['physical_change'] ?>
                        </td>

                        <td data-label="🧠 Инт" class="<?= $act['intellectual_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['intellectual_change'] ?>
                        </td>

                        <td data-label="🧘 Дух" class="<?= $act['spiritual_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['spiritual_change'] ?>
                        </td>

                        <td data-label="🍔 Жир" class="<?= $act['obesity_change'] <= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['obesity_change'] ?>
                        </td>

                        <td data-label="🔥 XP" class="pos">
                            +<?= (int)$act['exp_change'] ?>
                        </td>

                        <td data-label="Действия" class="actions">
                            <a href="edit_activity.php?id=<?= (int)$act['id'] ?>" class="btn-edit">✏️</a>
                            <a href="activ_stats.php?id=<?= (int)$act['id'] ?>" 
                               class="btn-delete"
                               onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php endforeach; ?>

    </div>
</main>