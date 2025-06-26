<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch module_batches
$module_batches = $pdo->query("SELECT mb.id, m.title AS module_title, b.name AS batch_name
                               FROM module_batches mb
                               JOIN modules m ON mb.module_id = m.id
                               JOIN batches b ON mb.batch_id = b.id
                               ORDER BY m.title, b.name")->fetchAll();

// Fetch all students
$students = $pdo->query("SELECT id, full_name, student_id FROM students ORDER BY full_name")->fetchAll();

// Enroll logic
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_batch_id = $_POST['module_batch_id'];
    $selected_students = $_POST['students'] ?? [];

    if (!empty($selected_students)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO student_enrollments (student_id, module_batch_id) VALUES (?, ?)");
        foreach ($selected_students as $student_id) {
            $stmt->execute([$student_id, $module_batch_id]);
        }
        $success = count($selected_students) . " student(s) enrolled successfully.";
    } else {
        $success = "No students were selected.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Enroll Students in Module-Batch</h3>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Select Module-Batch</label>
        <select name="module_batch_id" class="form-control" required>
            <option value="">-- Select Module-Batch --</option>
            <?php foreach ($module_batches as $mb): ?>
                <option value="<?= $mb['id'] ?>">
                    <?= htmlspecialchars($mb['module_title']) ?> - <?= htmlspecialchars($mb['batch_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
       <input type="text" id="studentSearch" class="form-control mb-2" placeholder="Search students by name or ID...">

<div class="form-check mb-2">
    <input type="checkbox" class="form-check-input" id="selectAllStudents">
    <label for="selectAllStudents" class="form-check-label">Select All / Deselect All</label>
</div>

<div id="studentList" class="border rounded p-2" style="max-height: 300px; overflow-y: scroll;">


<div id="studentList" class="border rounded p-2" style="max-height: 300px; overflow-y: scroll;">
    <?php foreach ($students as $stu): ?>
        <div class="form-check student-item">
            <input class="form-check-input" type="checkbox" name="students[]" value="<?= $stu['id'] ?>" id="stu<?= $stu['id'] ?>">
            <label class="form-check-label" for="stu<?= $stu['id'] ?>">
                <?= htmlspecialchars($stu['full_name']) ?> (<?= $stu['student_id'] ?>)
            </label>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.getElementById('studentSearch').addEventListener('input', function () {
        const search = this.value.toLowerCase();
        const items = document.querySelectorAll('.student-item');

        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(search) ? '' : 'none';
        });
    });
</script>
<script>
    document.getElementById('selectAllStudents').addEventListener('change', function () {
        const checked = this.checked;
        const checkboxes = document.querySelectorAll('.student-item input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = checked);
    });
</script>

    </div>

    <button type="submit" class="btn btn-primary">Enroll Selected</button>
    <a href="student_by_module_batch.php" class="btn btn-secondary">Back</a>
</form>

<?php include '../includes/footer.php'; ?>
