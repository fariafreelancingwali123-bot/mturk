<?php
session_start();
require 'includes/db.php';

// Check if user is requester and logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'requester') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $deadline = $_POST['deadline'];
    $payment = $_POST['payment'];
    $requester_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tasks (requester_id, title, description, category, deadline, payment, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'open', NOW())");
    $stmt->execute([$requester_id, $title, $description, $category, $deadline, $payment]);

    header('Location: marketplace.php');
    exit();
}
?>

<form method="post">
  <input type="text" name="title" placeholder="Task Title" required />
  <textarea name="description" placeholder="Task Description" required></textarea>
  <select name="category" required>
    <option value="data entry">Data Entry</option>
    <option value="survey">Survey</option>
    <option value="transcription">Transcription</option>
  </select>
  <input type="date" name="deadline" required />
  <input type="number" name="payment" placeholder="Payment Amount" step="0.01" required />
  <button type="submit">Post Task</button>
</form>
