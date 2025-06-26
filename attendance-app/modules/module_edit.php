<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: modules.php");
    exit();
}

// Fetch current module
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$id]);
$module = $stmt->fetch();

if (!$module) {
    echo "Module not found.";
    exit();
}

// Fetch module types
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
        $stmt = $pdo->prepare("UPDATE modules SET code = ?, title = ?, type_id = ? WHERE id = ?");
        $stmt->execute([$code, $title, $type_id, $id]);
        header("Location: modules.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h3>Edit Module</h3>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Module Code</label>
        <input type="text" name="code" class="form-control" required value="<?= htmlspecialchars($module['code']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Module Title</label>
        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($module['title']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Module Type</label>
        <select name="type_id" class="form-control" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>" <?= $type['id'] == $module['type_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['type_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="modules.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include '../includes/footer.php'; ?>
