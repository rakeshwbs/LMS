<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle);
        $expected = ['student_id', 'full_name', 'email', 'phone'];
        $matched = array_map('strtolower', $header) === $expected;

        if (!$matched) {
            $feedback = "<div class='alert alert-danger'>Invalid CSV header. Use: student_id, full_name, email, phone</div>";
        } else {
            $stmt = $pdo->prepare("INSERT IGNORE INTO students (student_id, full_name, email, phone) VALUES (?, ?, ?, ?)");
            $count = 0;
            while (($row = fgetcsv($handle)) !== false) {
                $stmt->execute([$row[0], $row[1], $row[2], $row[3]]);
                $count++;
            }
            $feedback = "<div class='alert alert-success'>$count students imported successfully.</div>";
        }

        fclose($handle);
    } else {
        $feedback = "<div class='alert alert-danger'>Failed to read CSV file.</div>";
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Import Students via CSV</h3>

<?= $feedback ?>

<form method="POST" enctype="multipart/form-data" class="mb-3">
    <div class="mb-3">
        <label class="form-label">Upload CSV File</label>
        <input type="file" name="csv_file" accept=".csv" class="form-control" required>
        <div class="form-text">CSV must include columns: student_id, full_name, email, phone</div>
    </div>
    <button type="submit" class="btn btn-primary">Import</button>
    <a href="student.php" class="btn btn-secondary">Back</a>
</form>

<?php include '../includes/footer.php'; ?>
