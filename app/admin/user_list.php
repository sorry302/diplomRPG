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
            👥 Управление пользователями
        </h3>

        <!-- ===== ФИЛЬТРЫ ===== -->
        <div class="filters" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <a href="user_list.php" class="filter-btn <?= !isset($_GET['role']) ? 'active' : '' ?>">Все</a>
            <a href="user_list.php?role=3" class="filter-btn <?= (($_GET['role'] ?? '') == '3') ? 'active' : '' ?>">Админы</a>
            <a href="user_list.php?role=2" class="filter-btn <?= (($_GET['role'] ?? '') == '2') ? 'active' : '' ?>">Пользователи</a>
            <a href="user_list.php?role=1" class="filter-btn <?= (($_GET['role'] ?? '') == '1') ? 'active' : '' ?>">Новички</a>
        </div>

        <div class="table-responsive">
            <table class="rpg-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя пользователя</th>
                        <th>Роль</th>
                        <th>Уровень</th>
                        <th>Опыт</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                $roleFilter = isset($_GET['role']) ? (int)$_GET['role'] : null;

                $sql = "
                    SELECT u.*, e.level, e.exp 
                    FROM users u
                    LEFT JOIN experience e ON u.id = e.user_id
                ";

                if ($roleFilter !== null) {
                    $sql .= " WHERE u.role = $roleFilter";
                }

                $sql .= " ORDER BY u.id DESC";

                $result = mysqli_query($conn, $sql);

                if ($result):
                    while ($user = mysqli_fetch_assoc($result)):

                        // ===== РОЛИ =====
                        $roleName = 'Неизвестно';
                        $roleClass = 'role-user';

                        switch ((int)$user['role']) {
                            case 3:
                                $roleName = 'Администратор';
                                $roleClass = 'role-admin';
                                break;
                            case 2:
                                $roleName = 'Пользователь';
                                break;
                            case 1:
                                $roleName = 'Новичок';
                                $roleClass = 'role-newbie';
                                break;
                        }

                        $level = isset($user['level']) ? (int)$user['level'] : 1;
                        $exp = isset($user['exp']) ? (int)$user['exp'] : 0;
                ?>

                    <tr>
                        <td>#<?= (int)$user['id'] ?></td>

                        <td style="font-weight: bold;">
                            <?= htmlspecialchars($user['username']) ?>
                        </td>

                        <td>
                            <span class="badge <?= $roleClass ?>">
                                <?= $roleName ?>
                            </span>
                        </td>

                        <td>
                            <span class="level-badge">⭐ <?= $level ?></span>
                        </td>

                        <td><?= $exp ?> XP</td>

                        <td class="actions">
                            <a href="edit_user.php?id=<?= (int)$user['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_user.php?id=<?= (int)$user['id'] ?>"
                               class="btn-delete"
                               onclick="return confirm('Вы уверены? Это действие необратимо!')">🗑️</a>
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