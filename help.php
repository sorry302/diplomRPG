<?php
session_start();
require 'app/functions/auth_check.php';
require_once __DIR__ . '/config/config.php';
require 'app/components/header.php';

$category = $_GET['categor'] ?? 'all';

if ($category === 'all') {
    $helps = mysqli_query($conn, " SELECT * FROM help  WHERE is_active = 1");
} else {
    $categorySafe = mysqli_real_escape_string($conn, $category);

    $helps = mysqli_query($conn, " SELECT * FROM help  WHERE is_active = 1  AND category = '$categorySafe'");
}
?>


<div class="help-page-container">
    <div class="help-menu">
        <a href="help.php?categor=all" class="<?= $category=='all'?'active':'' ?>">✨ Все</a>
        <a href="help.php?categor=guide" class="<?= $category=='guide'?'active':'' ?>">📖 Инструкция</a>
        <a href="help.php?categor=faq" class="<?= $category=='faq'?'active':'' ?>">❓ Вопросы</a>
        <a href="help.php?categor=tips" class="<?= $category=='tips'?'active':'' ?>">💡 Советы</a>
    </div>

    <div class="help-container">
        <?php while($help = mysqli_fetch_assoc($helps)): ?>
            <div class="help-item" onclick="this.classList.toggle('active')">
                <div class="help-header">
                    <h3><?= $help['title'] ?></h3>
                </div>
                <div class="help-content">
                    <?= $help['content'] ?>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if(mysqli_num_rows($helps) == 0): ?>
            <div class="no-results">
                <p>В этой категории пока нет записей...</p>
            </div>
        <?php endif; ?>
    </div>
</div>