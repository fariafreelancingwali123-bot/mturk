<?php
session_start();
require 'db.php';

// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'requester') {
    header("Location: login.php");
    exit();
}

$requester_id = $_SESSION['user_id'];

// Handle task posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $payment = $_POST['payment'];
    $deadline = $_POST['deadline'];

    if ($title && $description && $category && $payment && $deadline) {
        $stmt = $conn->prepare("INSERT INTO tasks (requester_id, title, description, category, payment, deadline, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isssds", $requester_id, $title, $description, $category, $payment, $deadline);
        if ($stmt->execute()) {
            $message = "Task posted successfully!";
        } else {
            $message = "Error posting task: " . $stmt->error;
        }
    } else {
        $message = "Please fill all fields.";
    }
}

// Fetch requester's tasks
$stmt = $conn->prepare("SELECT * FROM tasks WHERE requester_id = ?");
$stmt->bind_param("i", $requester_id);
$stmt->execute();
$tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requester Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0; padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            border: 1.5px solid white;
            padding: 7px 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        header a:hover {
            background-color: white;
            color: #333;
        }
        section {
            max-width: 900px;
            background: white;
            margin: 30px auto;
            padding: 25px 40px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin: 15px 0 6px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="number"],
        form input[type="date"],
        form select,
        form textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1.5px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        form textarea {
            resize: vertical;
            min-height: 80px;
        }
        form button {
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        p.message {
            background-color: #e6ffe6;
            color: #2b7a2b;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        p.no-tasks {
            color: #777;
            font-style: italic;
        }
        @media (max-width: 600px) {
            section {
                padding: 20px;
            }
            header h1 {
                font-size: 20px;
            }
            form button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>Requester Dashboard</h1>
    <a href="logout.php">Logout</a>
</header>

<section>
    <?php if (isset($message)): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Post a New Task</h2>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="Data Entry">Data Entry</option>
            <option value="Surveys">Surveys</option>
            <option value="Transcription">Transcription</option>
        </select>

        <label for="payment">Payment ($):</label>
        <input type="number" name="payment" id="payment" step="0.01" required>

        <label for="deadline">Deadline:</label>
        <input type="date" name="deadline" id="deadline" required>

        <button type="submit">Post Task</button>
    </form>

    <h2>Your Posted Tasks</h2>
    <?php if ($tasks->num_rows > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Payment</th>
                <th>Deadline</th>
                <th>Created</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $tasks->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td>$<?php echo number_format($row['payment'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['deadline']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-tasks">You haven't posted any tasks yet.</p>
    <?php endif; ?>
</section>
</body>
</html>
