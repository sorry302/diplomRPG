<?php
session_start();
require '../functions/db.php';

if(isset($_POST['save_activ'])){
$name = $_POST['name'];
$category = $_POST['category'];
$health_change = $_POST['health_change'];
$physical_change = $_POST['physical_change'];
$intellectual_change = $_POST['intellectual_change'];
$spiritual_change = $_POST['spiritual_change'];
$obesity_change = $_POST['obesity_change'];
$exp_change = $_POST['exp_change'];

$query = "INSERT INTO activities (name, category, health_change, physical_change, intellectual_change, spiritual_change, obesity_change, exp_change) 
          VALUES ('$name', '$category', '$health_change', '$physical_change', '$intellectual_change', '$spiritual_change', '$obesity_change', '$exp_change')";

mysqli_query($conn, $query);

header('Location: activ_stats.php');
}

?>