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

    .filter-btn {
        padding: 8px 16px;
        background: #2a2a2e;
        border: 1px solid #555;
        color: #ccc;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s;
        font-size: 14px;
    }

    .filter-btn:hover {
        border-color: var(--accent-gold, #ffd700);
        color: #fff;
    }

    .filter-btn.active {
        background: var(--accent-gold, #ffd700);
        color: #000;
        border-color: var(--accent-gold, #ffd700);
        font-weight: bold;
    }

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
    }

    .role-admin { background: #721c24; color: #f8d7da; border: 1px solid #f5c6cb; }
    .role-user { background: #155724; color: #d4edda; border: 1px solid #c3e6cb; }
    .role-newbie { background: #0c5460; color: #d1ecf1; border: 1px solid #bee5eb; }

    .level-badge {
        background: #333;
        padding: 3px 7px;
        border-radius: 10px;
        border: 1px solid var(--accent-gold, #ffd700);
        color: var(--accent-gold, #ffd700);
    }

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

    /* Адаптивность */
    @media (max-width: 768px) {
        .admin-container {
            padding: 10px;
        }

        .panel {
            padding: 15px;
        }

        .filters {
            flex-wrap: wrap;
            gap: 8px !important;
        }

        .filter-btn {
            flex: 1 1 calc(50% - 8px);
            text-align: center;
            padding: 10px 5px;
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
<main class="admin-container">    <div class="panel">

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
                        <td data-label="ID">#<?= (int)$user['id'] ?></td>

                        <td data-label="Имя" style="font-weight: bold;">
                            <?= htmlspecialchars($user['username']) ?>
                        </td>

                        <td data-label="Роль">
                            <span class="badge <?= $roleClass ?>">
                                <?= $roleName ?>
                            </span>
                        </td>

                        <td data-label="Уровень">
                            <span class="level-badge">⭐ <?= $level ?></span>
                        </td>

                        <td data-label="Опыт"><?= $exp ?> XP</td>

                        <td data-label="Действия" class="actions">
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