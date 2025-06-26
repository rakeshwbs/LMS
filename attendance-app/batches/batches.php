<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Handle new batch
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_name = trim($_POST['batch_name']);

    if ($batch_name) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO batches (name) VALUES (?)");
        $stmt->execute([$batch_name]);
        header("Location: batches.php");
        exit();
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM batches WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: batches.php");
    exit();
}

// Get all batches
$batches = $pdo->query("SELECT * FROM batches ORDER BY name ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Manage Batches</h3>

<form method="POST" class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="batch_name" class="form-control" placeholder="Enter batch name (e.g., June 2025)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Add Batch</button>
        </div>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Batch Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($batches as $index => $batch): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($batch['name']) ?></td>
            <td>
                <a href="batches.php?delete=<?= $batch['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this batch?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
