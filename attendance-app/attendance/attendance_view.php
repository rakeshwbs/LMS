<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Load only batches (modules will load dynamically via AJAX)
$batches = $pdo->query("SELECT id, name FROM batches ORDER BY name")->fetchAll();

$records = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module_id'];
    $batch_id = $_POST['batch_id'];
    $date = $_POST['date'];

    $stmt = $pdo->prepare("
        SELECT s.full_name, s.student_id, a.status
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE a.module_id = ? AND a.batch_id = ? AND a.date = ?
        ORDER BY s.full_name
    ");
    $stmt->execute([$module_id, $batch_id, $date]);
    $records = $stmt->fetchAll();
}
?>

<?php include '../includes/header.php'; ?>

<h3>View Attendance</h3>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-4">
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

    <div class="col-md-1 align-self-end">
        <button type="submit" class="btn btn-info w-100">View</button>
    </div>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <?php if (count($records) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['student_id']) ?></td>
                    <td><?= htmlspecialchars($r['full_name']) ?></td>
                    <td><?= $r['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No attendance records found for the selected date and module.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- JavaScript to load module list based on selected batch -->
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
