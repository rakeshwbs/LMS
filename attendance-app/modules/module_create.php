<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch module types (e.g., NCC, LUC)
$types = $pdo->query("SELECT * FROM module_types ORDER BY type_name ASC")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $title = trim($_POST['title']);
    $type_id = $_POST['type_id'];

    if (!$code || !$title || !$type_id) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO modules (code, title, type_id) VALUES (?, ?, ?)");
        $stmt->execute([$code, $title, $type_id]);
        header("Location: modules.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Add New Module</h3>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Module Code</label>
        <input type="text" name="code" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Module Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Module Type</label>
        <select name="type_id" class="form-control" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="modules.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>
