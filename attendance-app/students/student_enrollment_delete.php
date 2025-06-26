<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the selected enrollment
    $stmt = $pdo->prepare("DELETE FROM student_module WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: student_enrollments.php?msg=deleted");
    exit();
} else {
    header("Location: student_enrollments.php");
    exit();
}
