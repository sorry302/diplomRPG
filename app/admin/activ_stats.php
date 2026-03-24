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
            ⚡ Управление активностями
        </h3>

        <!-- ===== ДОБАВЛЕНИЕ ===== -->
        <details style="margin-bottom: 20px; background: rgba(0,0,0,0.2); border-radius: 10px; padding: 10px;">
            <summary style="cursor: pointer; font-weight: bold; color: var(--accent-positive);">
                + Добавить новую активность
            </summary>

            <form method="POST" action="save_activity.php"
                  style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">

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

            $catKeySafe = mysqli_real_escape_string($conn, $catKey);

            $result = mysqli_query($conn, "
                SELECT * FROM activities 
                WHERE category = '$catKeySafe' 
                ORDER BY id DESC
            ");

            if (!$result || mysqli_num_rows($result) === 0) continue;
        ?>

        <h4 style="color: var(--text-muted); margin-bottom: 10px; border-left: 3px solid var(--accent-gold); padding-left: 10px;">
            <?= htmlspecialchars($catName) ?>
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
                        <td style="font-weight: bold;">
                            <?= htmlspecialchars($act['name']) ?>
                        </td>

                        <td class="<?= $act['health_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['health_change'] ?>
                        </td>

                        <td class="<?= $act['physical_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['physical_change'] ?>
                        </td>

                        <td class="<?= $act['intellectual_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['intellectual_change'] ?>
                        </td>

                        <td class="<?= $act['spiritual_change'] >= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['spiritual_change'] ?>
                        </td>

                        <td class="<?= $act['obesity_change'] <= 0 ? 'pos' : 'neg' ?>">
                            <?= (int)$act['obesity_change'] ?>
                        </td>

                        <td class="pos">
                            +<?= (int)$act['exp_change'] ?>
                        </td>

                        <td class="actions">
                            <a href="edit_activity.php?id=<?= (int)$act['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_activity.php?id=<?= (int)$act['id'] ?>" 
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