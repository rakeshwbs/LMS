<?php
session_start();
if (!isset($_SESSION['lecturer'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<h3>Welcome, <?= htmlspecialchars($_SESSION['lecturer']) ?>!</h3>
<p class="lead">This is your dashboard. Use the navigation menu to manage modules, students, and attendance.</p>

<?php include 'includes/footer.php'; ?>
