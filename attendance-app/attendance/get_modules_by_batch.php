<?php
require_once '../helpers/db.php';

if (isset($_GET['batch_id'])) {
    $batch_id = $_GET['batch_id'];

    $stmt = $pdo->prepare("
        SELECT DISTINCT m.id, m.title 
        FROM student_module sm
        JOIN modules m ON sm.module_id = m.id
        WHERE sm.batch_id = ?
        ORDER BY m.title
    ");
    $stmt->execute([$batch_id]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($modules);
}
