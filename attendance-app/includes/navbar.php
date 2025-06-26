<?php if (isset($_SESSION['lecturer'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php">Attendance</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>modules/modules.php">Modules</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>modules/module_types.php">Module Types</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>batches/batches.php">Batches</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>modules/module_batches.php">Module-Batch</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>class_groups/class_group.php">Class Groups</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>students/student.php">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>students/student_by_module_batch.php">Students by Module-Batch</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>attendance/attendance.php">Take Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>attendance/attendance_view.php">View Attendance</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>logout.php">Logout</a></li>2
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
