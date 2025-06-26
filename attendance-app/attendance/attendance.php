<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Load batches (modules are loaded via AJAX)
$batches = $pdo->query("SELECT id, name FROM batches ORDER BY name")->fetchAll();

$students = [];
$showForm = false;

if (isset($_POST['load_students'])) {
    $module_id = $_POST['module_id'];
    $batch_id = $_POST['batch_id'];
    $date = $_POST['date'];

    if ($module_id && $batch_id && $date) {
        $stmt = $pdo->prepare("
            SELECT s.id, s.full_name 
            FROM student_module sm
            JOIN students s ON sm.student_id = s.id
            WHERE sm.module_id = ? AND sm.batch_id = ?
            ORDER BY s.full_name
        ");
        $stmt->execute([$module_id, $batch_id]);
        $students = $stmt->fetchAll();
        $showForm = true;
    }
}

// Save attendance
if (isset($_POST['submit_attendance'])) {
    $module_id = $_POST['module_id'];
    $batch_id = $_POST['batch_id'];
    $date = $_POST['date'];

    foreach ($_POST['status'] as $student_id => $status) {
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, module_id, batch_id, date, status)
                               VALUES (?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE status = VALUES(status)");
        $stmt->execute([$student_id, $module_id, $batch_id, $date, $status]);
    }

    header("Location: attendance.php?msg=saved");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<h3>Take Attendance</h3>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
    <div class="alert alert-success">Attendance saved successfully.</div>
<?php endif; ?>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Batch</label>
        <select name="batch_id" id="batchDropdown" class="form-control" required>
            <option value="">-- Select Batch --</option>
            <?php foreach ($batches as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Module</label>
        <select name="module_id" id="moduleDropdown" class="form-control" required>
            <option value="">-- Select Batch First --</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Date</label>
        <input type="date" name="date" class="form-control" required>
    </div>

    <div class="col-md-2 align-self-end">
        <button type="submit" name="load_students" class="btn btn-primary">Load Students</button>
    </div>
</form>

<?php if ($showForm && count($students) > 0): ?>
<form method="POST">
    <input type="hidden" name="module_id" value="<?= htmlspecialchars($module_id) ?>">
    <input type="hidden" name="batch_id" value="<?= htmlspecialchars($batch_id) ?>">
    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $s): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td>
                    <select name="status[<?= $s['id'] ?>]" class="form-control" required>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" name="submit_attendance" class="btn btn-success">Save Attendance</button>
</form>
<?php elseif ($showForm): ?>
    <div class="alert alert-warning">No students found for this module and batch.</div>
<?php endif; ?>

<script>
document.getElementById('batchDropdown').addEventListener('change', function () {
    const batchId = this.value;
    const moduleDropdown = document.getElementById('moduleDropdown');

    moduleDropdown.innerHTML = '<option value="">Loading...</option>';

    if (batchId) {
        fetch('get_modules_by_batch.php?batch_id=' + batchId)
            .then(response => response.json())
            .then(data => {
                moduleDropdown.innerHTML = '<option value="">-- Select Module --</option>';
                data.forEach(function (mod) {
                    const option = document.createElement('option');
                    option.value = mod.id;
                    option.textContent = mod.title;
                    moduleDropdown.appendChild(option);
                });
            });
    } else {
        moduleDropdown.innerHTML = '<option value="">-- Select Batch First --</option>';
    }
});
</script>

<?php include '../includes/footer.php'; ?>
