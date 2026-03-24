<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>
<main class="admin-container">
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold);">🍎 Управление продуктами</h3>
        
        <!-- Форма добавления -->
        <details style="margin-bottom: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive);">+ Добавить новый продукт</summary>
            <form method="POST" action="save_food.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                <input name="name" placeholder="Название (например: Яблоко)" required>
                <select name="type" style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;>
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

        <!-- Таблица продуктов -->
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
                    $stmt = $db->query("SELECT * FROM foods ORDER BY id DESC");
                    while ($food = $stmt->fetch(PDO::FETCH_ASSOC)):
                        $typeClass = 'type-' . $food['type'];
                    ?>
                    <tr>
                        <td>#<?= $food['id'] ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($food['name']) ?></td>
                        <td><span class="badge <?= $typeClass ?>"><?= $food['type'] ?></span></td>
                        <td><?= $food['calories'] ?></td>
                        <td class="<?= $food['health_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= ($food['health_change'] > 0 ? '+' : '') . $food['health_change'] ?>
                        </td>
                        <td class="<?= $food['obesity_change'] <= 0 ? 'pos' : 'neg' ?>">
                            <?= ($food['obesity_change'] > 0 ? '+' : '') . $food['obesity_change'] ?>
                        </td>
                        <td class="actions">
                            <a href="edit_food.php?id=<?= $food['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_food.php?id=<?= $food['id'] ?>" class="btn-delete" onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style>
.admin-container { max-width: 900px; margin: 20px auto; padding: 0 15px; }
.rpg-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
.rpg-table th { text-align: left; padding: 12px; color: var(--text-muted); border-bottom: 1px solid var(--border-soft); }
.rpg-table td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.03); }

.badge { padding: 2px 8px; border-radius: 6px; font-size: 11px; text-transform: uppercase; font-weight: bold; }
.type-healthy { background: rgba(61,220,132,0.15); color: #3ddc84; }
.type-neutral { background: rgba(255,255,255,0.1); color: #fff; }
.type-junk { background: rgba(255,107,107,0.15); color: #ff6b6b; }

.pos { color: var(--accent-positive); }
.neg { color: var(--accent-negative); }

.actions { display: flex; gap: 8px; }
.btn-edit, .btn-delete { text-decoration: none; padding: 5px; background: rgba(255,255,255,0.05); border-radius: 6px; transition: 0.2s; }
.btn-edit:hover { background: rgba(255,255,255,0.15); }
.btn-delete:hover { background: rgba(255,107,107,0.2); }

.btn-add { grid-column: span 2; background: var(--accent-positive); color: #000; border: none; padding: 10px; border-radius: 8px; font-weight: bold; cursor: pointer; }

input, select { background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 8px; border-radius: 6px; }
</style>