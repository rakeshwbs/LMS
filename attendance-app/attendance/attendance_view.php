<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../helpers/db.php';

$students = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleBatchId = $_POST['module_batch_id'];
    $date = $_POST['date'];

  $stmt = $pdo->prepare("
    SELECT s.full_name, sa.status
    FROM student_attendance sa
    JOIN student_enrollments se ON sa.student_enrollment_id = se.id
    JOIN students s ON se.student_id = s.id
    WHERE se.module_batch_id = ? AND sa.attendance_date = ?
");

    $stmt->execute([$moduleBatchId, $date]);
    $students = $stmt->fetchAll();
}


$batchStmt = $pdo->query("
    SELECT mb.id, CONCAT(m.title, ' - ', b.name) AS module_batch_name
    FROM module_batches mb
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
");
$moduleBatches = $batchStmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h3>View Attendance</h3>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label">Module–Batch</label>
            <select name="module_batch_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($moduleBatches as $mb): ?>
                    <option value="<?= $mb['id'] ?>" <?= isset($moduleBatchId) && $moduleBatchId == $mb['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($mb['module_batch_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date ?? '') ?>" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <?php if (!empty($students)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['fullname']) ?></td>
                        <td><?= htmlspecialchars($record['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-info">No attendance records found for the selected date.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
