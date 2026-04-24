<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../components/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Обработка обновления
if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = isset($_POST['role']) ? (int)$_POST['role'] : null;
    $password = $_POST['password'];

    $update_fields = [
        "`username` = '$username'",
        "`email` = '$email'"
    ];

    // Если роль передана (значит она была доступна для редактирования)
    if ($role !== null) {
        $update_fields[] = "`role` = $role";
    }

    // Если пароль введен
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_fields[] = "`password` = '$hashed_password'";
    }

    $sql = "UPDATE `users` SET " . implode(', ', $update_fields) . " WHERE `id` = $id";
    $conn->query($sql);
   
    header("Location: user_list.php");
    exit();
}

// Получение данных для формы
$result = $conn->query("SELECT * FROM `users` WHERE `id` = $id");
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<div style='color: white; padding: 20px;'>Пользователь не найден. <a href='user_list.php'>Назад</a></div>";
    exit;
}
?>

<style>
    .admin-container {
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        min-height: 80vh;
    }

    .panel {
        background: rgba(30, 30, 35, 0.9);
        border: 1px solid #444;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .edit-form {
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 20px; 
        margin-top: 20px;
    }

    .edit-form label {
        color: var(--accent-gold, #ffd700);
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .edit-form input, .edit-form textarea, .edit-form select {
        background: #2a2a2e;
        border: 1px solid #555;
        color: #fff;
        padding: 12px;
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
        font-family: inherit;
        font-size: 15px;
        transition: border-color 0.3s;
    }

    .edit-form input:focus, .edit-form select:focus {
        border-color: var(--accent-gold, #ffd700);
        outline: none;
    }

    .edit-form input:disabled, .edit-form select:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: #1a1a1e;
    }

    .btn-save {
        background: var(--accent-positive, #28a745);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        font-size: 16px;
        margin-top: 10px;
    }

    .btn-save:hover {
        filter: brightness(1.2);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        margin-bottom: 20px;
        color: #aaa;
        text-decoration: none;
        transition: 0.3s;
        font-size: 14px;
    }

    .btn-back:hover {
        color: var(--accent-gold, #ffd700);
    }

    .info-text {
        font-size: 12px;
        color: #888;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 15px;
        }
        .panel {
            padding: 15px;
        }
    }
</style>

<main class="admin-container">
    <a href="user_list.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>👤</span> Редактирование: <?= htmlspecialchars($user['username']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Имя пользователя</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
            </div>

            <div>
                <label>Новый пароль</label>
                <input type="password" name="password" placeholder="Оставьте пустым, если не хотите менять">
                <p class="info-text">Пароль будет автоматически захеширован перед сохранением.</p>
            </div>
            
            <div>
                <label>Роль в системе</label>
                <?php if ($user['role'] == 2 || $user['role'] == 3): ?>
                    <select name="role">
                        <option value="2" <?= $user['role'] == 2 ? 'selected' : '' ?>>Пользователь</option>
                        <option value="3" <?= $user['role'] == 3 ? 'selected' : '' ?>>Администратор</option>
                    </select>
                    <p class="info-text" style="color: var(--accent-positive);">Вы можете изменить права доступа (Пользователь/Администратор).</p>
                <?php else: ?>
                    <select disabled>
                        <option><?= $user['role'] == 1 ? 'Новичок' : 'Другое' ?></option>
                    </select>
                    <p class="info-text">Изменение роли для новичков (роль 1) недоступно в этом меню.</p>
                <?php endif; ?>
            </div>
            <button type="submit" name="update_user" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>
