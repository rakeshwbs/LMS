<?php if (isset($_SESSION['lecturer'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php">Attendance</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="../modules/modules.php">Modules</a></li>
                <li class="nav-item"><a class="nav-link" href="../modules/module_types.php">Module Types</a></li>
                <li class="nav-item"><a class="nav-link" href="../batches/batches.php">Batches</a></li>
                <li class="nav-item"><a class="nav-link" href="../students/students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="../attendance/attendance.php">Take Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="../attendance/attendance_view.php">View Attendance</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
