<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle task application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    // Check if already applied
    $check = $conn->prepare("SELECT * FROM worker_tasks WHERE worker_id = ? AND task_id = ?");
    $check->bind_param("ii", $user_id, $task_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        // Insert new application
        $apply = $conn->prepare("INSERT INTO worker_tasks (worker_id, task_id, status, submitted_at) VALUES (?, ?, 'pending', NOW())");
        $apply->bind_param("ii", $user_id, $task_id);
        $apply->execute();
        $message = "Task applied successfully!";
    } else {
        $message = "You have already applied for this task.";
    }
}

// Fetch available tasks (excluding those already applied to)
$sql = "SELECT t.* FROM tasks t
        WHERE t.id NOT IN (
            SELECT task_id FROM worker_tasks WHERE worker_id = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Tasks - MicroTasker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>Available Tasks</h1>
    <a href="worker_dashboard.php">My Dashboard</a> | 
    <a href="logout.php" style="color:white;">Logout</a>
</header>

<section>
    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($tasks->num_rows > 0): ?>
        <table border="1" cellpadding="10" style="margin: auto; background:#fff;">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Payment</th>
                <th>Deadline</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $tasks->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>$<?php echo number_format($row['payment'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['deadline']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Apply</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tasks available at the moment. Please check back later.</p>
    <?php endif; ?>
</section>
</body>
</html>
