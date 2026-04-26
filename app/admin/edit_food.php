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
if (isset($_POST['update_food'])) {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $calories = (int)$_POST['calories'];
    $health_change = (int)$_POST['health_change'];
    $obesity_change = (int)$_POST['obesity_change'];

    $conn->query("UPDATE `foods` SET 
        `name` = '$name', 
        `type` = '$type', 
        `calories` = $calories, 
        `health_change` = $health_change, 
        `obesity_change` = $obesity_change 
        WHERE `id` = $id");
   
    header("Location: eat_state.php");
    exit();
}

// Получение данных для формы
$result = $conn->query("SELECT * FROM `foods` WHERE `id` = $id");
$food = mysqli_fetch_assoc($result);

if (!$food) {
    echo "<div style='color: white; padding: 20px;'>Продукт не найден. <a href='eat_state.php'>Назад</a></div>";
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
    }
</style>

<main class="admin-container">
    <a href="eat_state.php" class="btn-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Назад к списку
    </a>
    
    <div class="panel">
        <h3 style="margin-top: 0; color: var(--accent-gold); display: flex; align-items: center; gap: 10px;">
            <span>🍎</span> Редактирование продукта: <?= htmlspecialchars($food['name']) ?>
        </h3>

        <form method="POST" class="edit-form">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <div>
                    <label>Название продукта</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($food['name']) ?>" required>
                </div>
                <div>
                    <label>Тип еды</label>
                    <select name="type">
                        <option value="healthy" <?= $food['type'] == 'healthy' ? 'selected' : '' ?>>Здоровая</option>
                        <option value="neutral" <?= $food['type'] == 'neutral' ? 'selected' : '' ?>>Нейтральная</option>
                        <option value="junk" <?= $food['type'] == 'junk' ? 'selected' : '' ?>>Вредная</option>
                    </select>
                </div>
            </div>

            <label>Характеристики (на 100г/порцию)</label>
            <div class="stats-grid">
                <div>
                    <label style="font-size: 11px; color: #aaa;">🔥 Калории</label>
                    <input type="number" name="calories" value="<?= $food['calories'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">❤️ Здоровье (+/-)</label>
                    <input type="number" name="health_change" value="<?= $food['health_change'] ?>" required>
                </div>
                <div>
                    <label style="font-size: 11px; color: #aaa;">🍔 Жир (+/-)</label>
                    <input type="number" name="obesity_change" value="<?= $food['obesity_change'] ?>" required>
                </div>
            </div>

            <button type="submit" name="update_food" class="btn-save">Сохранить изменения</button>
        </form>
    </div>
</main>
