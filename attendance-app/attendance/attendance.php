<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$students = [];
$date = '';
$module_batch_id = '';
$step = 1;
$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 1) {
    $module_batch_id = $_POST['module_batch_id'];
    $date = $_POST['date'];
    $step = 2;

    $stmt = $pdo->prepare("SELECT s.id, s.full_name, s.student_id
                           FROM students s
                           JOIN student_enrollments se ON s.id = se.student_id
                           WHERE se.module_batch_id = ?
                           ORDER BY s.full_name");
    $stmt->execute([$module_batch_id]);
    $students = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == 2) {
    $module_batch_id = $_POST['module_batch_id'];
    $date = $_POST['date'];
    $attendance = $_POST['attendance'];

    foreach ($attendance as $student_id => $status) {
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, module_batch_id, date, status)
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE status = VALUES(status)");
        $stmt->execute([$student_id, $module_batch_id, $date, $status]);
    }

    $feedback = "Attendance recorded successfully.";
    $step = 1;
}

// Get module-batch list
$module_batches = $pdo->query("SELECT mb.id, m.title AS module_title, b.name AS batch_name
                               FROM module_batches mb
                               JOIN modules m ON mb.module_id = m.id
                               JOIN batches b ON mb.batch_id = b.id
                               ORDER BY m.title, b.name")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Take Attendance</h3>

<?php if ($feedback): ?>
    <div class="alert alert-success"><?= $feedback ?></div>
<?php endif; ?>

<?php if ($step == 1): ?>
<form method="POST" class="row g-3">
    <input type="hidden" name="step" value="1">
    <div class="col-md-5">
        <label>Module-Batch</label>
        <select name="module_batch_id" class="form-control" required>
            <option value="">-- Select Module-Batch --</option>
            <?php foreach ($module_batches as $mb): ?>
                <option value="<?= $mb['id'] ?>"><?= htmlspecialchars($mb['module_title']) ?> - <?= htmlspecialchars($mb['batch_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label>Date</label>
        <input type="date" name="date" class="form-control" required>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Load Students</button>
    </div>
</form>

<?php elseif ($step == 2): ?>
<form method="POST">
    <input type="hidden" name="step" value="2">
    <input type="hidden" name="module_batch_id" value="<?= htmlspecialchars($module_batch_id) ?>">
    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

    <h5>Date: <?= htmlspecialchars($date) ?></h5>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $s): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?> (<?= $s['student_id'] ?>)</td>
                    <td>
                        <select name="attendance[<?= $s['id'] ?>]" class="form-control">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-success">Submit Attendance</button>
    <a href="attendance.php" class="btn btn-secondary">Cancel</a>
</form>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
