<?php
session_start();
require '../functions/db.php';

$help_list = $conn -> query("SELECT * FROM `help` WHERE `id` = '{$_GET['id']}'");
$helps = $help_list->fetch_assoc();

if(isset($_GET['id'])){
    if($helps['is_active'] == 1){
        $conn ->query("UPDATE `help` SET `is_active`='2' WHERE `id` = '{$_GET['id']}'");
        header("Location: admin_help.php");
    }else{
        $conn ->query("UPDATE `help` SET `is_active`='1' WHERE `id` = '{$_GET['id']}'");
        header("Location: admin_help.php");
    }

} 

if(isset($_GET['delete'])){
    $conn -> query("DELETE FROM `help` WHERE `id` = '{$_GET['delete']}'");
    header("Location: admin_help.php");
}