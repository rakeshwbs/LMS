<?php
require_once '../includes/header.php';
require_once '../helpers/db.php';

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // Skip the header row

        $rowCount = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $code = trim($data[0]);
            $title = trim($data[1]);
            $type_name = trim($data[2]);

            if (!$code || !$title || !$type_name) {
                continue;
            }

            // Resolve type_id
            $stmt = $pdo->prepare("SELECT id FROM module_types WHERE type_name = ?");
            $stmt->execute([$type_name]);
            $type = $stmt->fetch();

            if ($type) {
                $type_id = $type['id'];

                // Avoid duplicates
                $check = $pdo->prepare("SELECT * FROM modules WHERE code = ?");
                $check->execute([$code]);

                if ($check->rowCount() === 0) {
                    $insert = $pdo->prepare("INSERT INTO modules (code, title, type_id) VALUES (?, ?, ?)");
                    $insert->execute([$code, $title, $type_id]);
                    $rowCount++;
                }
            }
        }

        fclose($handle);
        $success = "$rowCount modules imported successfully.";
    } else {
        $error = "Unable to open the uploaded file.";
    }
}
?>

<div class="container mt-4">
    <h3>Import Modules from CSV</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Upload CSV File</label>
            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        </div>
        <button type="submit" class="btn btn-primary">Import CSV</button>
        <a href="modules.php" class="btn btn-secondary">Back to Modules</a>
    </form>

    <div class="alert alert-info">
        <strong>Expected CSV format:</strong><br>
        <code>code,title,type_name</code><br>
        Example:<br>
        <code>CS101,Introduction to C++,Core</code><br>
        <code>CS102,Web Technologies,Elective</code>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
