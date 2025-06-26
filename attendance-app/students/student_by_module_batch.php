<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$students = [];
$module_batch_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_batch_id = $_POST['module_batch_id'];

    $stmt = $pdo->prepare("
        SELECT s.student_id, s.full_name, s.email, s.phone
        FROM student_enrollments se
        JOIN students s ON se.student_id = s.id
        WHERE se.module_batch_id = ?
        ORDER BY s.full_name
    ");
    $stmt->execute([$module_batch_id]);
    $students = $stmt->fetchAll();
}

// Load all module-batch options
$module_batches = $pdo->query("
    SELECT mb.id, m.title AS module_title, b.name AS batch_name
    FROM module_batches mb
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
    ORDER BY m.title, b.name
")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>View Enrolled Students by Module-Batch</h3>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-6">
        <label>Select Module-Batch</label>
        <select name="module_batch_id" class="form-control" required>
            <option value="">-- Choose Module-Batch --</option>
            <?php foreach ($module_batches as $mb): ?>
                <option value="<?= $mb['id'] ?>" <?= $mb['id'] == $module_batch_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mb['module_title']) ?> - <?= htmlspecialchars($mb['batch_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">View Students</button>
    </div>
</form>

<?php if (!empty($students)): ?>
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
            <?php foreach ($students as $index => $stu): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($stu['student_id']) ?></td>
                    <td><?= htmlspecialchars($stu['full_name']) ?></td>
                    <td><?= htmlspecialchars($stu['email']) ?></td>
                    <td><?= htmlspecialchars($stu['phone']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning">No students are enrolled in this module-batch.</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
