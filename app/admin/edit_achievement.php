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
if (isset($_POST['update_achievement'])) {
    $code = $_POST['code'];
    $title = $_POST['title'];
    $icon = $_POST['icon'];
    $condition_type = $_POST['condition_type'];
    $condition_value = (int)$_POST['condition_value'];
    $description = $_POST['description'];

    $conn->query("UPDATE `achievements` SET `code` = '$code', `title` = '$title', `icon` = '$icon', `description` = '$description' WHERE `id` = $id");
    $conn->query("UPDATE `achievement_conditions` SET `condition_type` = '$condition_type', `condition_value` = $condition_value WHERE `achievement_id` = $id");
   
    header("Location: achievements.php");
    exit();
}

// Получение данных для формы
$result = $conn->query("
    SELECT a.*, ac.condition_type, ac.condition_value 
    FROM `achievements` a
    LEFT JOIN `achievement_conditions` ac ON a.id = ac.achievement_id
    WHERE a.`id` = $id
");
$achievement = mysqli_fetch_assoc($result);
if (!$achievement) {
    echo "<div style='color: white; padding: 20px;'>Достижение не найдено. <a href='achievements.php'>Назад</a></div>";
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

    .edit-form input, .edit-form select, .edit-form textarea {
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

    .edit-form input:focus, .edit-form select:focus, .edit-form textarea:focus {
        border-color: var(--accent-gold, #ffd700);
        outline: none;
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
    }
</style>

<main class="admin-container">
    <a href="achievements.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>🏆</span> Редактирование достижения: <?= htmlspecialchars($achievement['title']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Код (уникальный ID)</label>
                    <input type="text" name="code" value="<?= htmlspecialchars($achievement['code']) ?>" required>
                </div>
                <div>
                    <label>Иконка (Emoji)</label>
                    <input type="text" name="icon" value="<?= htmlspecialchars($achievement['icon']) ?>" required>
                </div>
            </div>

            <div>
                <label>Название</label>
                <input type="text" name="title" value="<?= htmlspecialchars($achievement['title']) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <div>
                    <label>Тип условия</label>
                    <select name="condition_type">
                        <option value="eat_count" <?= $achievement['condition_type'] == 'eat_count' ? 'selected' : '' ?>>Съесть X раз (порции)</option>
                        <option value="activity_count" <?= $achievement['condition_type'] == 'activity_count' ? 'selected' : '' ?>>Сделать X действий (всего)</option>
                        <option value="level_reach" <?= $achievement['condition_type'] == 'level_reach' ? 'selected' : '' ?>>Достичь уровня</option>
                        <option value="exp" <?= $achievement['condition_type'] == 'exp' ? 'selected' : '' ?>>Набрать опыта</option>
                        <option value="stat_physical" <?= $achievement['condition_type'] == 'stat_physical' ? 'selected' : '' ?>>Физическая форма ≥ X</option>
                        <option value="stat_intellect" <?= $achievement['condition_type'] == 'stat_intellect' ? 'selected' : '' ?>>Интеллект ≥ X</option>
                        <option value="stat_spiritual" <?= $achievement['condition_type'] == 'stat_spiritual' ? 'selected' : '' ?>>Духовность ≥ X</option>
                    </select>
                <div>

                </div>
                <div>
                    <label>Значение</label>
                    <input type="number" name="condition_value" value="<?= $achievement['condition_value'] ?>" required>
                </div>
            </div>
            <div>
                <label>Описание</label>
                <textarea name="description" rows="3" required><?= htmlspecialchars($achievement['description']) ?></textarea>
            </div>

            <button type="submit" name="update_achievement" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>
</main>
