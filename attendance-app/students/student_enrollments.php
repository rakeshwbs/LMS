<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all enrollments
$stmt = $pdo->query("
    SELECT se.id AS enrollment_id, s.full_name, s.student_id, m.title AS module_title, b.name AS batch_name
    FROM student_enrollments se
    JOIN students s ON se.student_id = s.id
    JOIN module_batches mb ON se.module_batch_id = mb.id
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
    ORDER BY m.title, b.name, s.full_name
");
$enrollments = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Student Enrollments</h3>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Module</th>
            <th>Batch</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enrollments as $index => $enr): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($enr['student_id']) ?></td>
            <td><?= htmlspecialchars($enr['full_name']) ?></td>
            <td><?= htmlspecialchars($enr['module_title']) ?></td>
            <td><?= htmlspecialchars($enr['batch_name']) ?></td>
            <td>
                <a href="student_enrollment_delete.php?id=<?= $enr['enrollment_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Unenroll this student?')">Unenroll</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
