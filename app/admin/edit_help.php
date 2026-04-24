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
if (isset($_POST['update_help'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    $conn->query("UPDATE `help` SET `title` = '$title', `content` = '$content', `category` = '$category' WHERE `id` = $id");
   
        header("Location: admin_help.php");
        exit();
    
}

// Получение данных для формы
$result = $conn->query("SELECT * FROM `help` WHERE `id` = $id");
$help = mysqli_fetch_assoc($result);

if (!$help) {
    echo "<div style='color: white; padding: 20px;'>Запись не найдена. <a href='admin_help.php'>Назад</a></div>";
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
    <a href="admin_help.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>📝</span> Редактирование: <?= htmlspecialchars($help['title']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div>
                <label>Заголовок помощи</label>
                <input type="text" name="title" value="<?= htmlspecialchars($help['title']) ?>" required placeholder="Введите заголовок...">
            </div>

            <div>
                <label>Категория</label>
                <select name="category">
                    <option value="guide" <?= $help['category'] == 'guide' ? 'selected' : '' ?>>Инструкция</option>
                    <option value="faq" <?= $help['category'] == 'faq' ? 'selected' : '' ?>>Вопросы/ответы</option>
                    <option value="tips" <?= $help['category'] == 'tips' ? 'selected' : '' ?>>Советы</option>
                </select>
            </div>

            <div>
                <label>Текст помощи</label>
                <textarea name="content" required placeholder="Опишите инструкции или ответы..."><?= htmlspecialchars($help['content']) ?></textarea>
            </div>

            <button type="submit" name="update_help" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>


