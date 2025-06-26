<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: modules.php");
    exit();
}

// First, check if module exists
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$id]);
$module = $stmt->fetch();

if (!$module) {
    echo "Module not found.";
    exit();
}

// Perform delete
$stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
$stmt->execute([$id]);

header("Location: modules.php");
exit();
