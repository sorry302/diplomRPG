<?php
session_start();
require '../functions/db.php';

if(isset($_GET['delete'])){
    $conn->query("DELETE FROM posts WHERE `id` = '{$_GET['delete']}'");
    header("Location: post.php");
}

?>