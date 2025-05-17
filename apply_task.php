<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $worker_id = $_SESSION['user_id'];

    // Check if already applied
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE task_id = ? AND worker_id = ?");
    $stmt->execute([$task_id, $worker_id]);
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO applications (task_id, worker_id, status) VALUES (?, ?, 'applied')");
        $stmt->execute([$task_id, $worker_id]);
        header('Location: dashboard.php');
        exit();
    } else {
        echo "You have already applied for this task.";
    }
}
?>
