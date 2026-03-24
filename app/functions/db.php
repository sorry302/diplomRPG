<?php
$host = '127.0.0.1';
$dbName = 'rpg';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$conn = mysqli_connect($host, $user, $pass, $dbName);

if (!$conn) {
    die("Ошибка подключения к БД: " . mysqli_connect_error());
}

mysqli_set_charset($conn, $charset);
