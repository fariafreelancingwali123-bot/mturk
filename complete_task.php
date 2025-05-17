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

    // Update application status
    $stmt = $pdo->prepare("UPDATE applications SET status = 'completed' WHERE task_id = ? AND worker_id = ?");
    $stmt->execute([$task_id, $worker_id]);

    // Update task status to completed if needed
    $stmt2 = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
    $stmt2->execute([$task_id]);

    header('Location: dashboard.php');
    exit();
}
?>
