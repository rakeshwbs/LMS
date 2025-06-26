<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: students.php");
    exit();
}

// Fetch the student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student not found.";
    exit();
}

// Fetch modules and batches for dropdowns
$modules = $pdo->query("SELECT id, title FROM modules ORDER BY title")->fetchAll();
$batches = $pdo->query("SELECT id, name FROM batches ORDER BY name")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);
    $batch_id = $_POST['batch_id'];
    $module_id = $_POST['module_id'];

    if (!$student_id || !$full_name || !$batch_id || !$module_id) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, batch_id = ?, module_id = ? WHERE id = ?");
        $stmt->execute([$student_id, $full_name, $batch_id, $module_id, $id]);
        header("Location: students.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Edit Student</h3>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Student ID</label>
        <input type="text" name="student_id" class="form-control" required value="<?= htmlspecialchars($student['student_id']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($student['full_name']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Batch</label>
        <select name="batch_id" class="form-control" required>
            <?php foreach ($batches as $batch): ?>
                <option value="<?= $batch['id'] ?>" <?= $batch['id'] == $student['batch_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($batch['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Module</label>
        <select name="module_id" class="form-control" required>
            <?php foreach ($modules as $mod): ?>
                <option value="<?= $mod['id'] ?>" <?= $mod['id'] == $student['module_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mod['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="students.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>
