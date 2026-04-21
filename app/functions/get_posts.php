<?php
require 'db.php';

$type = $_GET['type'] ?? 'all';

if ($type === 'all') {
    $sql = "SELECT * FROM posts";
} else {
    $type = mysqli_real_escape_string($conn, $type);
    $sql = "SELECT * FROM posts WHERE type = '$type'";
}

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "
    <div class='post' onclick='togglePost(this)'>
        <div class='post-header'>
            <span class='post-title'>{$row['title']}</span>
            <span class='arrow'>▾</span>
        </div>
        <div class='post-content'>{$row['content']}</div>
    </div>
    ";
}