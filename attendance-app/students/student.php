<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY full_name");
$students = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Students</h3>
<a href="student_create.php" class="btn btn-success mb-3">Add New Student</a>
<a href="student_enroll.php" class="btn btn-primary mb-3 ms-2">Enroll Student</a>
<a href="student_import.php" class="btn btn-outline-secondary mb-3 ms-2">Import from CSV</a>
<a href="student_export.php" class="btn btn-outline-success mb-3">Export Students to CSV</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $index => $student): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($student['student_id']) ?></td>
            <td><?= htmlspecialchars($student['full_name']) ?></td>
            <td><?= htmlspecialchars($student['email']) ?></td>
            <td><?= htmlspecialchars($student['phone']) ?></td>
            <td>
                <a href="student_edit.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="student_delete.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
