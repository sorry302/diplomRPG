<?php
session_start();

require 'app/functions/auth_check.php';
require_once __DIR__ . '/config/config.php';

$actionFeedback = $_SESSION['action_feedback'] ?? null;
unset($_SESSION['action_feedback']);

require 'app/functions/db.php'; // тут должен быть $conn
require 'app/components/header.php';
require 'app/functions/get_user_stats.php';

$health    = $stats['health'] ?? 0;
$fat       = $stats['fat'] ?? 0;
$intellect = $stats['intellect'] ?? 0;
$physical  = $stats['physical'] ?? 0;
$spiritual = $stats['spiritual'] ?? 0;

if(isset($_GET['type'])){
$post = $conn->query("SELECT * FROM `posts` WHERE `type` = '{$_GET['type']}'");
}else{
  $post = $conn->query("SELECT * FROM `posts`");
}
?>

<link rel="stylesheet" href="assets/css/stat.css">
<link rel="stylesheet" href="assets/css/modal.css">


<div class="main-layout">

<aside class="left-panel">
    <?php 
$currentType = $_GET['type'] ?? 'all';
?>

<div class="menu_left">
  <a href="index.php" class="<?= $currentType == 'all' ? 'active' : '' ?>">Все</a>
  <a href="index.php?type=info" class="<?= $currentType == 'info' ? 'active' : '' ?>">Новости</a>
  <a href="index.php?type=motivation" class="<?= $currentType == 'motivation' ? 'active' : '' ?>">Мотивация</a>
  <a href="index.php?type=warning" class="<?= $currentType == 'warning' ? 'active' : '' ?>">Предупреждение</a>
  <a href="index.php?type=event" class="<?= $currentType == 'event' ? 'active' : '' ?>">Событие</a>
</div>
     <?php foreach($post as $value){ ?>
  <div class="post" onclick="togglePost(this)">
    
    <div class="post-header">
      <span class="post-title">
        <?= $value['title'] ?>
      </span>

      <span class="arrow">▾</span>
    </div>

    <div class="post-content">
      <?= $value['content'] ?>
    </div>

  </div>
<?php } ?>
    </aside>

<main class="center-panel">

<div class="actions-floating">

  <!-- 🍽 -->
  <div class="action-wrapper">
    <div class="action-btn">🍽</div>

    <form action="/app/functions/eat_food.php" method="post" class="action-card">
      <div class="action-title">Приём пищи</div>

      <select name="food_id" required>
        <?php
        $foodsResult = mysqli_query($conn, "SELECT * FROM foods");
        while ($food = mysqli_fetch_assoc($foodsResult)) {
          echo "<option value='{$food['id']}'>{$food['name']} ({$food['calories']} ккал)</option>";
        }
        ?>
      </select>

      <input type="number" name="portions" min="1" value="1">
      <button type="submit">Поесть</button>
    </form>
  </div>

  <!-- 🏃 -->
  <div class="action-wrapper">
    <div class="action-btn">🏃</div>

    <form action="/app/functions/physical_activity.php" method="post" class="action-card">
      <div class="action-title">Физическая</div>

      <select name="activity_id" required>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM activities WHERE category='physical'");
        while ($a = mysqli_fetch_assoc($result)) {
          echo "<option value='{$a['id']}'>{$a['name']}</option>";
        }
        ?>
      </select>

      <input type="number" name="quantity" min="1" value="1">
      <button type="submit">Выполнить</button>
    </form>
  </div>

  <!-- 🧬 -->
  <div class="action-wrapper">
    <div class="action-btn">🧬</div>

    <form action="/app/functions/intellectual_activity.php" method="post" class="action-card">
      <div class="action-title">Интеллект</div>

      <select name="activity_id" required>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM activities WHERE category='intellectual'");
        while ($a = mysqli_fetch_assoc($result)) {
          echo "<option value='{$a['id']}'>{$a['name']}</option>";
        }
        ?>
      </select>

      <input type="number" name="quantity" min="1" value="1">
      <button type="submit">Выполнить</button>
    </form>
  </div>

  <!-- 🧘 -->
  <div class="action-wrapper">
    <div class="action-btn">🧘‍♂️</div>

    <form action="/app/functions/spiritual_activity.php" method="post" class="action-card">
      <div class="action-title">Духовность</div>

      <select name="activity_id" required>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM activities WHERE category='spiritual'");
        while ($a = mysqli_fetch_assoc($result)) {
          echo "<option value='{$a['id']}'>{$a['name']}</option>";
        }
        ?>
      </select>

      <input type="number" name="quantity" min="1" value="1">
      <button type="submit">Выполнить</button>
    </form>
  </div>

  <!-- 🚬 -->
  <div class="action-wrapper">
    <div class="action-btn">🚬</div>

    <form action="/app/functions/bad_hobby.php" method="post" class="action-card">
      <div class="action-title">Плохие привычки</div>

      <select name="activity_id" required>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM activities WHERE category='bad_habit'");
        while ($a = mysqli_fetch_assoc($result)) {
          echo "<option value='{$a['id']}'>{$a['name']}</option>";
        }
        ?>
      </select>

      <input type="number" name="quantity" min="1" value="1">
      <button type="submit">Выполнить</button>
    </form>
  </div>

</div>


  <!-- ===== ПАНЕЛЬ ПЕРСОНАЖА ===== -->
  <section class="panel character-panel">
    <h4 class="panel-title">Характеристики</h4>

    <?php
    function renderStat($label, $value, $class) {
      $value = min(100, (int)$value);
      echo "
      <div class='stat'>
        <div class='stat-label'>{$label}</div>
        <div class='progress'>
          <div class='progress-bar {$class}' style='width: {$value}%;'>
            {$value}%
          </div>
        </div>
      </div>";
    }

    renderStat('Здоровье', $health, 'health');
    renderStat('Жирность', $fat, 'fat');
    renderStat('Интеллект', $intellect, 'intellect');
    renderStat('Физическая', $physical, 'energy');
    renderStat('Духовность', $spiritual, 'spiritual');
    ?>



  </section>




  <!-- ===== МОДАЛКА ===== -->
  <div id="profileModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close">&times;</span>

      <h2>Создание персонажа</h2>
      <p>Укажи параметры героя, чтобы начать прокачку.</p>

      <form method="POST" action="/app/functions/save_profile.php">
        <input type="number" name="age" placeholder="Возраст" required>
        <input type="number" name="height" placeholder="Рост (см)" required>
        <input type="number" name="weight" placeholder="Вес (кг)" required>
        <button type="submit">Создать персонажа</button>
      </form>
    </div>
  </div>

</main>

<aside class="right-panel">
        <div class="notification unread">
          <p>🏆 Новое достижение!</p>
        </div>
    </aside>

</div>

<!-- КНОПКИ (видны только на мобиле) -->
<div class="mobile-buttons">
    <button id="openPosts">📜</button>
    <button id="openNotifications">🔔</button>
</div>

<!-- ЛЕВАЯ ПАНЕЛЬ -->
<div class="drawer left-drawer" id="postsDrawer">
    <button class="close-btn">✖</button>
    <h3>Посты</h3>
    <?php 
$currentType = $_GET['type'] ?? 'all';
?>

<div class="menu_left">
  <a href="index.php" class="<?= $currentType == 'all' ? 'active' : '' ?>">Все</a>
  <a href="index.php?type=info" class="<?= $currentType == 'info' ? 'active' : '' ?>">Новости</a>
  <a href="index.php?type=motivation" class="<?= $currentType == 'motivation' ? 'active' : '' ?>">Мотивация</a>
  <a href="index.php?type=warning" class="<?= $currentType == 'warning' ? 'active' : '' ?>">Предупреждение</a>
  <a href="index.php?type=event" class="<?= $currentType == 'event' ? 'active' : '' ?>">Событие</a>
</div>
    <?php foreach($post as $value){ ?>
  <div class="post" onclick="togglePost(this)">
    
    <div class="post-header">
      <span class="post-title">
        <?= $value['title'] ?>
      </span>

      <span class="arrow">▾</span>
    </div>

    <div class="post-content">
      <?= $value['content'] ?>
    </div>

  </div>
<?php } ?>
</div>

<!-- ПРАВАЯ ПАНЕЛЬ -->
<div class="drawer right-drawer" id="notificationsDrawer">
    <button class="close-btn">✖</button>
    <h3>Уведомления</h3>
</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<?php if ($actionFeedback): ?>
<script>
  window.__ACTION_FEEDBACK__ = <?= json_encode($actionFeedback, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php endif; ?>



  <script src="/assets/js/modal.js" defer></script>
  <script src="/assets/js/post_click.js" defer></script>
  <script src="/assets/js/effectavto.js" defer></script>
  <script src="/assets/js/effectras.js" defer></script>
  <script src="/assets/js/main.js" defer></script>
  <script src="/assets/js/draivpostyved.js" defer></script>

</body>
</html>