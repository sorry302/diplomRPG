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
if (isset($_POST['update_activity'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $health_change = $_POST['health_change'];
    $physical_change = $_POST['physical_change'];
    $intellectual_change = $_POST['intellectual_change'];
    $spiritual_change = $_POST['spiritual_change'];
    $obesity_change = $_POST['obesity_change'];
    $exp_change = $_POST['exp_change'];

    $conn->query("UPDATE `activities` SET `name` = '$name', `category` = '$category', `health_change` = $health_change, `physical_change` = $physical_change, `intellectual_change` = $intellectual_change, `spiritual_change` = $spiritual_change, `obesity_change` = $obesity_change, `exp_change` = $exp_change WHERE `id` = $id");
   
    header("Location: activ_stats.php");
    exit();
}

// Получение данных для формы
$result = $conn->query("SELECT * FROM `activities` WHERE `id` = $id");
$activity = mysqli_fetch_assoc($result);

if (!$activity) {
    echo "<div style='color: white; padding: 20px;'>Активность не найдена. <a href='activ_stats.php'>Назад</a></div>";
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

    .edit-form input, .edit-form select {
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
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
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<main class="admin-container">
    <a href="activ_stats.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>⚡</span> Редактирование активности: <?= htmlspecialchars($activity['name']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <div>
                    <label>Название активности</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($activity['name']) ?>" required>
                </div>
                <div>
                    <label>Категория</label>
                    <select name="category">
                        <option value="physical" <?= $activity['category'] == 'physical' ? 'selected' : '' ?>>Физическая</option>
                        <option value="intellectual" <?= $activity['category'] == 'intellectual' ? 'selected' : '' ?>>Интеллектуальная</option>
                        <option value="spiritual" <?= $activity['category'] == 'spiritual' ? 'selected' : '' ?>>Духовная</option>
                        <option value="bad_habit" <?= $activity['category'] == 'bad_habit' ? 'selected' : '' ?>>Вредная привычка</option>
                    </select>
                </div>
            </div>

            <label>Изменение характеристик</label>
            <div class="stats-grid">
                <div>
                    <label style="font-size: 11px; color: #aaa;">❤️ HP</label>
                    <input type="number" name="health_change" value="<?= $activity['health_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">💪 Физ. форма</label>
                    <input type="number" name="physical_change" value="<?= $activity['physical_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">🧠 Интеллект</label>
                    <input type="number" name="intellectual_change" value="<?= $activity['intellectual_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">🧘 Духовность</label>
                    <input type="number" name="spiritual_change" value="<?= $activity['spiritual_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">🍔 Жир</label>
                    <input type="number" name="obesity_change" value="<?= $activity['obesity_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">🔥 Опыт</label>
                    <input type="number" name="exp_change" value="<?= $activity['exp_change'] ?>" required>
                </div>
            </div>

            <button type="submit" name="update_activity" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>
