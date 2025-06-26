<?php
require_once __DIR__ . '/../helpers/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = (strpos($_SERVER['PHP_SELF'], '/modules/') !== false ||
         strpos($_SERVER['PHP_SELF'], '/batches/') !== false ||
         strpos($_SERVER['PHP_SELF'], '/students/') !== false ||
         strpos($_SERVER['PHP_SELF'], '/attendance/') !== false ||
         strpos($_SERVER['PHP_SELF'], '/reports/') !== false ||
         strpos($_SERVER['PHP_SELF'], '/class_groups/') !== false)
         ? '../' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="<?= $base ?>assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include $base . 'includes/navbar.php'; ?>

<div class="container mt-4">
