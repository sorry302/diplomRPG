<?php
session_start();
require '../functions/db.php';

if(isset($_POST['save_food'])){
    
$name = $_POST['name'];
$type = $_POST['type'];
$calories = $_POST['calories'];
$health_change = $_POST['health_change'];
$obesity_change = $_POST['obesity_change'];

$query = "INSERT INTO `foods`(`name`, `type`, `calories`, `health_change`, `obesity_change`) VALUES ('$name','$type','$calories','$health_change','$obesity_change')";

mysqli_query($conn, $query);

header('Location: eat_state.php');
}

?>