<?php
require_once '../includes/header.php';
require_once '../helpers/db.php';

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$module_batch_id = $_GET['module_batch_id'] ?? null;

if (!$module_batch_id) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Module-Batch ID missing.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Get module-batch label
$stmt = $pdo->prepare("
    SELECT CONCAT(m.title, ' - ', b.name) AS module_batch_label
    FROM module_batches mb
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
    WHERE mb.id = ?
");
$stmt->execute([$module_batch_id]);
$labelRow = $stmt->fetch();
$module_batch_label = $labelRow['module_batch_label'] ?? 'Unknown';


// Fetch enrolled students
$stmt = $pdo->prepare("
    SELECT s.student_id, s.fullname, s.email, s.phone
    FROM student_enrollments se
    JOIN students s ON se.student_id = s.id
    WHERE se.module_batch_id = ?
");
$stmt->execute([$module_batch_id]);
$students = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h3>Enrolled Students for <?= htmlspecialchars($module_batch_label) ?></h3>

    <a href="student.php" class="btn btn-secondary mb-3">Back to Students</a>

    <?php if (count($students) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($students as $s): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($s['student_id']) ?></td>
                <td><?= htmlspecialchars($s['fullname']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['phone']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info">No students enrolled for this module-batch.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
