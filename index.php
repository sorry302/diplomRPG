<?php
require 'app/functions/auth_check.php';
require_once __DIR__ . '/config/config.php';
$actionFeedback = $_SESSION['action_feedback'] ?? null;
unset($_SESSION['action_feedback']);

require 'app/functions/db.php';
require 'app/components/header.php';
require 'app/functions/get_user_stats.php';


$health = $stats['health'] ?? 0;
$fat = $stats['fat'] ?? 0;
$intellect = $stats['intellect'] ?? 0;
$physical = $stats['physical'] ?? 0;
$spiritual = $stats['spiritual'] ?? 0;
?>


<link rel="stylesheet" href="assets/css/stat.css">
<link rel="stylesheet" href="assets/css/modal.css">

<main>

  <!-- ===== ПАНЕЛЬ ПЕРСОНАЖА ===== -->
  <section class="panel character-panel">
    <h4 class="panel-title">Характеристики</h4>

    <div class="stat">
      <div class="stat-label">Здоровье</div>
      <div class="progress">
        <div class="progress-bar health" style="width: <?= min(100, $health) ?>%;">
          <?= $health ?>%
        </div>
      </div>
    </div>

    <div class="stat">
      <div class="stat-label">Жирность</div>
      <div class="progress">
        <div class="progress-bar fat" style="width: <?= min(100, $fat) ?>%;">
          <?= $fat ?>%
        </div>
      </div>
    </div>

    <div class="stat">
      <div class="stat-label">Инттелект</div>
      <div class="progress">
        <div class="progress-bar intellect" style="width: <?= min(100, $intellect) ?>%;">
          <?= $intellect ?>%
        </div>
      </div>
    </div>

    <div class="stat">
      <div class="stat-label">Физическая</div>
      <div class="progress">
        <div class="progress-bar energy" style="width: <?= min(100, $physical) ?>%;">
          <?= $physical ?>%
        </div>
      </div>
    </div>

    <div class="stat">
      <div class="stat-label">Духовность</div>
      <div class="progress">
        <div class="progress-bar spiritual" style="width: <?= min(100, $spiritual) ?>%;">
          <?= $spiritual ?>%
        </div>
      </div>
    </div>
  </section>


  <!-- ===== ДЕЙСТВИЯ ===== -->
  <section class="panel actions-panel"> 
    <h4 class="panel-title">Действия</h4>

    <!-- ЕДА -->
    <form action="/app/functions/eat_food.php" method="post" class="action-card" id="food">
      <div class="action-header">
        <span class="action-icon">🍽</span>
        <span class="action-title">Приём пищи</span>
      </div>

      <select name="food_id" required>
        <?php
        $foods = $db->query("SELECT * FROM foods")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($foods as $food) {
          echo "<option value='{$food['id']}'>
            {$food['name']} ({$food['calories']} ккал)
          </option>";
        }
        ?>
      </select>

      <input
        type="number"
        name="portions"
        min="1"
        value="1"
      >

      <button type="submit">Поесть</button>
    </form>

    <!-- Физические -->
   <form action="/app/functions/physical_activity.php" method="post" class="action-card" id="activity">

  <div class="action-header">
    <span class="action-icon">🏃</span>
    <span class="action-title">Физическая активность</span>
  </div>

  <select name="activity_id" required>
    <?php
    $activities = $db->query("
      SELECT * FROM activities WHERE category = 'physical'
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($activities as $a) {
      echo "<option value='{$a['id']}'>
        {$a['name']}
      </option>";
    }
    ?>
  </select>

  <input type="number" name="quantity" min="1" value="1">

  <button type="submit">Выполнить</button>
</form>

    <!-- Инттелект -->
   <form action="/app/functions/intellectual_activity.php" method="post" class="action-card" id="intellect">

  <div class="action-header">
    <span class="action-icon">🧬</span>
    <span class="action-title">Инттелект</span>
  </div>

  <select name="activity_id" required>
    <?php
    $activities = $db->query("
      SELECT * FROM activities WHERE category = 'intellectual'
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($activities as $a) {
      echo "<option value='{$a['id']}'>
        {$a['name']}
      </option>";
    }
    ?>
  </select>

  <input type="number" name="quantity" min="1" value="1">

  <button type="submit">Выполнить</button>
</form>

    <!-- духовность -->
   <form action="/app/functions/spiritual_activity.php" method="post" class="action-card" id="spiritual">

  <div class="action-header">
    <span class="action-icon">🧘‍♂️</span>
    <span class="action-title">Духовность</span>
  </div>

  <select name="activity_id" required>
    <?php
    $activities = $db->query("
      SELECT * FROM activities WHERE category = 'spiritual'
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($activities as $a) {
      echo "<option value='{$a['id']}'>
        {$a['name']}
      </option>";
    }
    ?>
  </select>

  <input type="number" name="quantity" min="1" value="1">

  <button type="submit">Выполнить</button>
</form>

  </section>


  <!-- ===== МОДАЛКА СОЗДАНИЯ ПЕРСОНАЖА ===== -->
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



<?php if ($actionFeedback): ?>
<script>
  window.__ACTION_FEEDBACK__ = <?= json_encode($actionFeedback, JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php endif; ?>

<footer>
  <?php require 'app/components/bottom_nav.php'; ?>
  

  <script src="/assets/js/modal.js" defer></script>
  <script src="/assets/js/effectavto.js" defer></script>
  <script src="/assets/js/effectras.js" defer></script>
</footer>
</body>
</html>
