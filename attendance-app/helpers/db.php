<?php
$host = 'localhost';
$dbname = 'attendance_db';       // Make sure this matches your MySQL DB
$user = 'root';                  // Change if needed
$pass = 'root';                      // Change to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
