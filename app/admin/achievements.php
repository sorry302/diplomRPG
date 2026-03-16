<?php
require_once __DIR__ . '/../functions/admin_check.php';
require_once __DIR__.'/../functions/db.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../components/header.php';
?>

<form method="POST" action="save_achievement.php">

<input name="code" placeholder="code">
<input name="title" placeholder="title">
<input name="description" placeholder="description">
<input name="icon" placeholder="🍽">

<select name="condition_type">
<option value="eat_count">Съесть X раз</option>
<option value="activity_count">Сделать X действий</option>
<option value="level_reach">Достичь уровня</option>
<option value="stat_physical">Physical stat</option>
</select>

<input name="condition_value" placeholder="value">

<button>Создать</button>

</form>
