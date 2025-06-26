<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $full_name = trim($_POST['full_name']);

    if ($student_id === '' || $full_name === '') {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name) VALUES (?, ?)");
        $stmt->execute([$student_id, $full_name]);
        header("Location: students.php?msg=created");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Add New Student</h3>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?= implode("<br>", $errors) ?>
    </div>
<?php endif; ?>

<form method="POST" class="col-md-6">
    <div class="mb-3">
        <label>Student ID</label>
        <input type="text" name="student_id" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Save Student</button>
    <a href="students.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>
