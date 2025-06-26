<?php
// Minimal auth.php file to satisfy the require_once
session_start();

if (!isset($_SESSION['lecturer_id'])) {
    header("Location: ../login.php");
    exit;
}
