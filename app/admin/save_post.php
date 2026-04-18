<?php
session_start();
require '../functions/db.php';


if (isset($_POST['add_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $type = $_POST['type'];    
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
    $created_by = $_SESSION['user']['user_id'] ?? 0;

    $query = "INSERT INTO posts (`title`, `content`, `type`, `is_active`, `is_pinned`, `created_by`) 
              VALUES ('$title', '$content', '$type', '$is_active', '$is_pinned', '$created_by')";
    
    mysqli_query($conn, $query);
    header('Location: post.php');
    exit;
}
?>