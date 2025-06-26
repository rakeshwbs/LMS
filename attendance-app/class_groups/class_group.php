<?php
require_once '../helpers/db.php';
session_start();

if (!isset($_SESSION['lecturer'])) {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_batch_id = $_POST['module_batch_id'];
    $group_name = $_POST['group_name'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $pdo->prepare("INSERT INTO class_groups (module_batch_id, group_name, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$module_batch_id, $group_name, $day_of_week, $start_time, $end_time]);
    $success = "Class group created successfully.";
}

// Fetch module_batches for dropdown
$stmt = $pdo->query("
    SELECT mb.id, CONCAT(m.title, ' - ', b.name) AS label
    FROM module_batches mb
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
    ORDER BY m.title, b.name
");
$module_batches = $stmt->fetchAll();

// Fetch existing class groups
$groupsStmt = $pdo->query("
    SELECT cg.*, CONCAT(m.title, ' - ', b.name) AS module_batch
    FROM class_groups cg
    JOIN module_batches mb ON cg.module_batch_id = mb.id
    JOIN modules m ON mb.module_id = m.id
    JOIN batches b ON mb.batch_id = b.id
    ORDER BY cg.day_of_week
");
$class_groups = $groupsStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Class Groups</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Attendance</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/modules/module.php">Modules</a></li>
        <li class="nav-item"><a class="nav-link" href="/modules/module_types.php">Module Types</a></li>
        <li class="nav-item"><a class="nav-link" href="/batches/batch.php">Batches</a></li>
        <li class="nav-item"><a class="nav-link" href="/module_batches/module_batch.php">Module-Batch</a></li>
        <li class="nav-item"><a class="nav-link active" href="/class_groups/class_group.php">Class Groups</a></li>
        <li class="nav-item"><a class="nav-link" href="/students/student.php">Students</a></li>
        <li class="nav-item"><a class="nav-link" href="/attendance/attendance_take.php">Take Attendance</a></li>
        <li class="nav-item"><a class="nav-link" href="/attendance/attendance_view.php">View Attendance</a></li>
      </ul>
      <span class="navbar-text">
        <a href="/logout.php" class="text-light">Logout</a>
      </span>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <h3>Manage Class Groups</h3>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Module - Batch</label>
            <select name="module_batch_id" class="form-select" required>
                <option value="">Select</option>
                <?php foreach ($module_batches as $mb): ?>
                    <option value="<?= $mb['id'] ?>"><?= htmlspecialchars($mb['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Group Name</label>
            <input type="text" name="group_name" class="form-control" required placeholder="e.g., Tuesday Group">
        </div>
        <div class="col-md-2">
            <label class="form-label">Day</label>
            <select name="day_of_week" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
                    <option value="<?= $day ?>"><?= $day ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label">Start</label>
            <input type="time" name="start_time" class="form-control" required>
        </div>
        <div class="col-md-1">
            <label class="form-label">End</label>
            <input type="time" name="end_time" class="form-control" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>

    <h5>Existing Class Groups</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Module - Batch</th>
                <th>Group Name</th>
                <th>Day</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($class_groups as $group): ?>
                <tr>
                    <td><?= htmlspecialchars($group['module_batch']) ?></td>
                    <td><?= htmlspecialchars($group['group_name']) ?></td>
                    <td><?= $group['day_of_week'] ?></td>
                    <td><?= substr($group['start_time'], 0, 5) ?> - <?= substr($group['end_time'], 0, 5) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
