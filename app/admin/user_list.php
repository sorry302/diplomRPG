<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>
<main class="admin-container">
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold);">👥 Управление пользователями</h3>
        
        <!-- Фильтры -->
        <div class="filters" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <a href="user_list.php" class="filter-btn <?= !isset($_GET['role']) ? 'active' : '' ?>">Все</a>
            <a href="user_list.php?role=3" class="filter-btn <?= ($_GET['role'] ?? '') == '3' ? 'active' : '' ?>">Админы</a>
            <a href="user_list.php?role=2" class="filter-btn <?= ($_GET['role'] ?? '') == '2' ? 'active' : '' ?>">Пользователи</a>
            <a href="user_list.php?role=1" class="filter-btn <?= ($_GET['role'] ?? '') == '1' ? 'active' : '' ?>">Новички</a>
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
                    $roleFilter = $_GET['role'] ?? null;
                    $sql = "
                        SELECT u.*, e.level, e.exp 
                        FROM users u
                        LEFT JOIN experience e ON u.id = e.user_id
                    ";
                    
                    if ($roleFilter !== null) {
                        $sql .= " WHERE u.role = :role";
                    }
                    
                    $sql .= " ORDER BY u.id DESC";
                    
                    $stmt = $db->prepare($sql);
                    if ($roleFilter !== null) {
                        $stmt->execute(['role' => (int)$roleFilter]);
                    } else {
                        $stmt->execute();
                    }
                    
                    while ($user = $stmt->fetch(PDO::FETCH_ASSOC)):
                        // Маппинг ролей
                        $roleName = 'Неизвестно';
                        $roleClass = 'role-user';
                        
                        switch((int)$user['role']) {
                            case 3:
                                $roleName = 'Администратор';
                                $roleClass = 'role-admin';
                                break;
                            case 2:
                                $roleName = 'Пользователь';
                                $roleClass = 'role-user';
                                break;
                            case 1:
                                $roleName = 'Новичок';
                                $roleClass = 'role-newbie';
                                break;
                        }

                        $level = $user['level'] ?? 1;
                        $exp = $user['exp'] ?? 0;
                    ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($user['username']) ?></td>
                        <td><span class="badge <?= $roleClass ?>"><?= $roleName ?></span></td>
                        <td><span class="level-badge">⭐ <?= $level ?></span></td>
                        <td><?= $exp ?> XP</td>
                        <td class="actions">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">✏️</a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Вы уверены, что хотите удалить пользователя? Это действие необратимо!')">🗑️</a>
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

.badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; text-transform: uppercase; font-weight: bold; }
.role-admin { background: rgba(228, 193, 109, 0.15); color: var(--accent-gold); border: 1px solid rgba(228, 193, 109, 0.3); }
.role-user { background: rgba(61, 220, 132, 0.1); color: var(--accent-positive); border: 1px solid rgba(61, 220, 132, 0.2); }
.role-newbie { background: rgba(255, 255, 255, 0.05); color: #8b90a0; border: 1px solid rgba(255, 255, 255, 0.1); }

.level-badge { color: var(--accent-positive); font-weight: bold; }

.filter-btn { text-decoration: none; color: var(--text-muted); padding: 6px 15px; background: rgba(255,255,255,0.05); border-radius: 8px; font-size: 13px; transition: 0.2s; }
.filter-btn:hover, .filter-btn.active { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid var(--border-soft); }
.filter-btn.active { border-color: var(--accent-gold); color: var(--accent-gold); }

.actions { display: flex; gap: 8px; }
.btn-edit, .btn-delete { text-decoration: none; padding: 5px; background: rgba(255,255,255,0.05); border-radius: 6px; transition: 0.2s; }
.btn-edit:hover { background: rgba(255,255,255,0.15); }
.btn-delete:hover { background: rgba(255,107,107,0.2); }
</style>