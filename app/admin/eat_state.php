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

        <h3 style="margin-top: 0; color: var(--accent-gold);">
            🍎 Управление продуктами
        </h3>

        <!-- ===== ДОБАВЛЕНИЕ ===== -->
        <details style="margin-bottom: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive);">
                + Добавить новый продукт
            </summary>

            <form method="POST" action="save_food.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">

                <input name="name" placeholder="Название (например: Яблоко)" required>

                <select name="type">
                    <option value="healthy">Здоровая</option>
                    <option value="neutral">Нейтральная</option>
                    <option value="junk">Вредная</option>
                </select>

                <input type="number" name="calories" placeholder="Калории" required>
                <input type="number" name="health_change" placeholder="Здоровье (+/-)" required>
                <input type="number" name="obesity_change" placeholder="Жир (+/-)" required>

                <button type="submit" class="btn-add">Сохранить</button>
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
                ?>

                    <tr>
                        <td>#<?= (int)$food['id'] ?></td>

                        <td style="font-weight: bold;">
                            <?= htmlspecialchars($food['name']) ?>
                        </td>

                        <td>
                            <span class="badge <?= htmlspecialchars($typeClass) ?>">
                                <?= htmlspecialchars($food['type']) ?>
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