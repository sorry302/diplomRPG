<?php
session_start();
require '../functions/db.php';

if (isset($_POST['add_help'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    mysqli_query($conn, "
        INSERT INTO help (title, content, category)
        VALUES ('$title', '$content', '$category')
    ");
    header("Location: admin_help.php");
}