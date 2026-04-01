<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';
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
    .add-food-form {
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 15px; 
        margin-top: 15px;
    }

    .add-food-form input, .add-food-form select {
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
        font-size: 15px;
    }

    .rpg-table th {
        background: rgba(0, 0, 0, 0.3);
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #444;
        color: var(--accent-gold, #ffd700);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 13px;
    }

    .rpg-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #333;
        vertical-align: middle;
    }

    .rpg-table tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .type-healthy { background: #155724; color: #d4edda; border: 1px solid #c3e6cb; }
    .type-neutral { background: #383d41; color: #e2e3e5; border: 1px solid #d6d8db; }
    .type-junk { background: #721c24; color: #f8d7da; border: 1px solid #f5c6cb; }

    .pos { color: #28a745; font-weight: bold; }
    .neg { color: #dc3545; font-weight: bold; }

    .actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit, .btn-delete {
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        transition: transform 0.2s;
    }

    .btn-edit:hover, .btn-delete:hover {
        transform: scale(1.2);
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
</style>

<main class="admin-container">
    <div class="panel">

        <h3 style="margin-top: 0; color: var(--accent-gold);">
            🍎 Управление продуктами
        </h3>

        <!-- ===== ДОБАВЛЕНИЕ ===== -->
        <details style="margin-bottom: 30px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive, #28a745);">
                + Добавить новый продукт
            </summary>

            <form method="POST" action="save_food.php" class="add-food-form">

                <input name="name" placeholder="Название (например: Яблоко)" required>

                <select name="type">
                    <option value="healthy">Здоровая</option>
                    <option value="neutral">Нейтральная</option>
                    <option value="junk">Вредная</option>
                </select>

                <input type="number" name="calories" placeholder="Калории" required>
                <input type="number" name="health_change" placeholder="Здоровье (+/-)" required>  
                <input type="number" name="obesity_change" placeholder="Жир (+/-)" required>

                <button type="submit" class="btn-add">Сохранить продукт</button>
            </form>
        </details>

        <!-- ===== ТАБЛИЦА ===== -->
        <div class="table-responsive">
            <table class="rpg-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Ккал</th>
                        <th>❤️ HP</th>
                        <th>🍔 Жир</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $result = mysqli_query($conn, "SELECT * FROM foods ORDER BY id DESC");

                if ($result):
                    while ($food = mysqli_fetch_assoc($result)):

                        $typeClass = 'type-' . $food['type'];
                        $typeName = 'Неизвестно';
                        switch($food['type']) {
                            case 'healthy': $typeName = 'Здоровая'; break;
                            case 'neutral': $typeName = 'Нейтральная'; break;
                            case 'junk': $typeName = 'Вредная'; break;
                        }
                ?>

                    <tr>
                        <td>#<?= (int)$food['id'] ?></td>

                        <td style="font-weight: bold;">
                            <?= htmlspecialchars($food['name']) ?>
                        </td>

                        <td>
                            <span class="badge <?= htmlspecialchars($typeClass) ?>">
                                <?= $typeName ?>
                            </span>
                        </td>

                        <td><?= (int)$food['calories'] ?></td>

                        <td class="<?= $food['health_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= ($food['health_change'] > 0 ? '+' : '') . (int)$food['health_change'] ?>  
                        </td>

                        <td class="<?= $food['obesity_change'] <= 0 ? 'pos' : 'neg' ?>">
                            <?= ($food['obesity_change'] > 0 ? '+' : '') . (int)$food['obesity_change'] ?>
                        </td>

                        <td class="actions">
                            <a href="edit_food.php?id=<?= (int)$food['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_food.php?id=<?= (int)$food['id'] ?>"
                               class="btn-delete"
                               onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>

                <?php
                    endwhile;
                endif;
                ?>

                </tbody>
            </table>
        </div>

    </div>
</main>