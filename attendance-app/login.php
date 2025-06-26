<?php
require_once 'helpers/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM lecturers WHERE username = ?");
    $stmt->execute([$username]);
    $lecturer = $stmt->fetch();

    if ($lecturer && password_verify($password, $lecturer['password'])) {
        $_SESSION['lecturer'] = $lecturer['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-4">
        <h3>Lecturer Login</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
