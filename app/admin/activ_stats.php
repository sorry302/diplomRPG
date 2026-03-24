<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>
<main class="admin-container">
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold);">⚡ Управление активностями</h3>
        
        <!-- Форма добавления -->
        <details style="margin-bottom: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive);">+ Добавить новую активность</summary>
            <form method="POST" action="save_activity.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                <input name="name" placeholder="Название (например: Бег)" required>
                <select name="category" style="width: 100%; background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 10px; border-radius: 8px;>
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
                <button type="submit" class="btn-add">Сохранить активность</button>
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
            $stmt = $db->prepare("SELECT * FROM activities WHERE category = ? ORDER BY id DESC");
            $stmt->execute([$catKey]);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($activities)) continue;
        ?>
        <h4 style="color: var(--text-muted); margin-bottom: 10px; border-left: 3px solid var(--accent-gold); padding-left: 10px;"><?= $catName ?></h4>
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
                    <?php foreach ($activities as $act): ?>
                    <tr>
                        <td style="font-weight: bold;"><?= htmlspecialchars($act['name']) ?></td>
                        <td class="<?= $act['health_change'] >= 0 ? 'pos' : 'neg' ?>"><?= $act['health_change'] ?></td>
                        <td class="<?= $act['physical_change'] >= 0 ? 'pos' : 'neg' ?>"><?= $act['physical_change'] ?></td>
                        <td class="<?= $act['intellectual_change'] >= 0 ? 'pos' : 'neg' ?>"><?= $act['intellectual_change'] ?></td>
                        <td class="<?= $act['spiritual_change'] >= 0 ? 'pos' : 'neg' ?>"><?= $act['spiritual_change'] ?></td>
                        <td class="<?= $act['obesity_change'] <= 0 ? 'pos' : 'neg' ?>"><?= $act['obesity_change'] ?></td>
                        <td class="pos">+<?= $act['exp_change'] ?></td>
                        <td class="actions">
                            <a href="edit_activity.php?id=<?= $act['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_activity.php?id=<?= $act['id'] ?>" class="btn-delete" onclick="return confirm('Удалить?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<style>
.admin-container { max-width: 1000px; margin: 20px auto; padding: 0 15px; }
.rpg-table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 13px; }
.rpg-table th { text-align: left; padding: 10px; color: var(--text-muted); border-bottom: 1px solid var(--border-soft); }
.rpg-table td { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.03); }

.pos { color: var(--accent-positive); }
.neg { color: var(--accent-negative); }

.actions { display: flex; gap: 8px; }
.btn-edit, .btn-delete { text-decoration: none; padding: 5px; background: rgba(255,255,255,0.05); border-radius: 6px; transition: 0.2s; }
.btn-edit:hover { background: rgba(255,255,255,0.15); }
.btn-delete:hover { background: rgba(255,107,107,0.2); }

.btn-add { grid-column: span 2; background: var(--accent-positive); color: #000; border: none; padding: 10px; border-radius: 8px; font-weight: bold; cursor: pointer; }

input, select { background: #0b0e14; border: 1px solid var(--border-soft); color: #fff; padding: 8px; border-radius: 6px; }
</style>