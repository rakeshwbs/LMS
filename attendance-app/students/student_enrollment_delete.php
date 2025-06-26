<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM student_enrollments WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: student_enrollments.php");
exit();
?>
