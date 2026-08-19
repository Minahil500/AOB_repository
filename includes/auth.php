<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /aob_repository/login.php");
    exit();
}
?>