<?php
session_start();
require '../functions/db.php';

if(isset($_GET['id'])){
    $conn->query("DELETE FROM users WHERE `id` = '{$_GET['id']}'");
    header("Location: user_list.php");
}

?>