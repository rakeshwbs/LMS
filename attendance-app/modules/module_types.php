<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = trim($_POST['type_name']);

    if ($type) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO module_types (type_name) VALUES (?)");
        $stmt->execute([$type]);
        header("Location: module_types.php");
        exit();
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM module_types WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: module_types.php");
    exit();
}

// Fetch existing types
$types = $pdo->query("SELECT * FROM module_types ORDER BY type_name ASC")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Module Types</h3>

<form method="POST" class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <input type="text" name="type_name" class="form-control" placeholder="Enter new type (e.g., NCC)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Add Type</button>
        </div>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Type Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($types as $index => $t): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($t['type_name']) ?></td>
            <td>
                <a href="module_types.php?delete=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this type?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
