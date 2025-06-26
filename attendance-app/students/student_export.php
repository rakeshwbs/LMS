<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="students_export.csv"');

$output = fopen('php://output', 'w');

// Column headings
fputcsv($output, ['student_id', 'full_name', 'email', 'phone']);

// Fetch and write rows
$stmt = $pdo->query("SELECT student_id, full_name, email, phone FROM students ORDER BY full_name");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit;
