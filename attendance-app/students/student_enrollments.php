<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all enrollments
$stmt = $pdo->query("
    SELECT sm.id, s.full_name, s.student_id, m.title AS module_title, b.name AS batch_name
    FROM student_module sm
    JOIN students s ON sm.student_id = s.id
    JOIN modules m ON sm.module_id = m.id
    JOIN batches b ON sm.batch_id = b.id
    ORDER BY s.full_name, b.name, m.title
");

$enrollments = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Student Enrollments</h3>

<a href="student_enroll.php" class="btn btn-primary mb-3">+ Enroll Another Student</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Module</th>
            <th>Batch</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enrollments as $index => $en): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($en['student_id']) ?></td>
            <td><?= htmlspecialchars($en['full_name']) ?></td>
            <td><?= htmlspecialchars($en['module_title']) ?></td>
            <td><?= htmlspecialchars($en['batch_name']) ?></td>
            <td>
                <a href="student_enrollment_delete.php?id=<?= $en['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this enrollment?')">Remove</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
