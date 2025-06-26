<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Handle insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_id = $_POST['module_id'];
    $batch_id = $_POST['batch_id'];

    // prevent duplicates
    $check = $pdo->prepare("SELECT * FROM module_batches WHERE module_id = ? AND batch_id = ?");
    $check->execute([$module_id, $batch_id]);

    if ($check->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO module_batches (module_id, batch_id) VALUES (?, ?)");
        $stmt->execute([$module_id, $batch_id]);
    }
    header("Location: module_batches.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM module_batches WHERE id = ?")->execute([$id]);
    header("Location: module_batches.php");
    exit();
}

// Fetch all module_batches
$stmt = $pdo->query("SELECT mb.id, m.code AS module_code, m.title AS module_title, b.name AS batch_name
                     FROM module_batches mb
                     JOIN modules m ON mb.module_id = m.id
                     JOIN batches b ON mb.batch_id = b.id
                     ORDER BY m.title, b.name");
$module_batches = $stmt->fetchAll();

// Fetch all modules and batches for dropdown
$modules = $pdo->query("SELECT * FROM modules ORDER BY title")->fetchAll();
$batches = $pdo->query("SELECT * FROM batches ORDER BY name")->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h3>Module-Batch Combinations</h3>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-5">
        <label>Module</label>
        <select name="module_id" class="form-control" required>
            <option value="">-- Select Module --</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?> (<?= $m['code'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-5">
        <label>Batch</label>
        <select name="batch_id" class="form-control" required>
            <option value="">-- Select Batch --</option>
            <?php foreach ($batches as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-success w-100">Add</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Module</th>
            <th>Batch</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($module_batches as $index => $mb): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($mb['module_title']) ?> (<?= $mb['module_code'] ?>)</td>
                <td><?= htmlspecialchars($mb['batch_name']) ?></td>
                <td>
                    <a href="?delete=<?= $mb['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this combination?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
