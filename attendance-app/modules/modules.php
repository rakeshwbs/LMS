<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all modules from DB
$stmt = $pdo->query("SELECT modules.*, module_types.type_name FROM modules 
                     JOIN module_types ON modules.type_id = module_types.id 
                     ORDER BY modules.title ASC");
$modules = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Modules</h3>
<a href="module_create.php" class="btn btn-success mb-3">Add New Module</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Module Code</th>
            <th>Title</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($modules as $index => $mod): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($mod['code']) ?></td>
            <td><?= htmlspecialchars($mod['title']) ?></td>
            <td><?= htmlspecialchars($mod['type_name']) ?></td>
            <td>
                <a href="module_edit.php?id=<?= $mod['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="module_delete.php?id=<?= $mod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this module?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
