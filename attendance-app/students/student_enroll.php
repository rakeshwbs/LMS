<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch students, modules, and batches
$students = $pdo->query("SELECT id, full_name FROM students ORDER BY full_name")->fetchAll();
$modules = $pdo->query("SELECT id, title FROM modules ORDER BY title")->fetchAll();
$batches = $pdo->query("SELECT id, name FROM batches ORDER BY name")->fetchAll();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $batch_id = $_POST['batch_id'];

    if (!$student_id || !$module_id || !$batch_id) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO student_module (student_id, module_id, batch_id) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $module_id, $batch_id]);
        $success = "Student enrolled successfully.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Enroll Student in Module</h3>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" class="col-md-6">
    <div class="mb-3">
        <label>Student</label>
        <select name="student_id" class="form-control" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Module</label>
        <select name="module_id" class="form-control" required>
            <option value="">-- Select Module --</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Batch</label>
        <select name="batch_id" class="form-control" required>
            <option value="">-- Select Batch --</option>
            <?php foreach ($batches as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Enroll</button>
    <a href="students.php" class="btn btn-secondary">Back</a>
</form>

<?php include '../includes/footer.php'; ?>
