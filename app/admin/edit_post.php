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
if (isset($_POST['update_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;

    $conn->query("UPDATE `posts` SET  `title` = '$title',  `content` = '$content',  `type` = '$type',  `is_active` = $is_active,  `is_pinned` = $is_pinned  WHERE `id` = $id");
   
    header("Location: post.php");
    exit();
}

// Получение данных для формы
$result = $conn->query("SELECT * FROM `posts` WHERE `id` = $id");
$post = mysqli_fetch_assoc($result);

if (!$post) {
    echo "<div style='color: white; padding: 20px;'>Пост не найден. <a href='post.php'>Назад</a></div>";
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

    .edit-form input:focus, .edit-form textarea:focus, .edit-form select:focus {
        border-color: var(--accent-gold, #ffd700);
        outline: none;
    }

    .edit-form textarea {
        resize: vertical;
        min-height: 200px;
    }

    .checkbox-group {
        display: flex;
        gap: 20px;
        align-items: center;
        background: rgba(255,255,255,0.05);
        padding: 15px;
        border-radius: 4px;
        border: 1px solid #444;
    }

    .checkbox-group label {
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        text-transform: none;
        letter-spacing: 0;
    }

    .checkbox-group input {
        width: auto;
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

    @media (max-width: 768px) {
        .admin-container {
            padding: 15px;
        }
        .panel {
            padding: 15px;
        }
        .btn-save {
            width: 100%;
        }
    }
</style>

<main class="admin-container">
    <a href="post.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>📝</span> Редактирование поста: <?= htmlspecialchars($post['title']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div>
                <label>Заголовок поста</label>
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required placeholder="Введите заголовок...">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Тип поста</label>
                    <select name="type">
                        <option value="info" <?= $post['type'] == 'info' ? 'selected' : '' ?>>📢 Новости</option>
                        <option value="motivation" <?= $post['type'] == 'motivation' ? 'selected' : '' ?>>💪 Мотивация</option>
                        <option value="warning" <?= $post['type'] == 'warning' ? 'selected' : '' ?>>⚠ Предупреждение</option>
                        <option value="event" <?= $post['type'] == 'event' ? 'selected' : '' ?>>🏆 Событие</option>
                    </select>
                </div>
                <div>
                    <label>Настройки</label>
                </div>
            </div>

            <div>
                <label>Текст поста</label>
                <textarea name="content" required placeholder="Введите текст поста..."><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <button type="submit" name="update_post" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>


