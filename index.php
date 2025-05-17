<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MicroTasker - Earn by Doing Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome to MicroTasker</h1>
        <nav>
            <a href="register.php">Register</a> |
            <a href="login.php">Login</a> |
            <a href="tasks.php">Browse Tasks</a>
        </nav>
    </header>

    <section class="intro">
        <h2>Earn Money by Completing Simple Tasks</h2>
        <p>Join as a worker to complete micro-tasks and earn money, or sign up as a requester to post tasks and get help from a global workforce.</p>
    </section>

    <section class="featured-tasks">
        <h2>Featured Tasks</h2>
        <ul>
            <li>📝 Data Entry - $1.00</li>
            <li>📋 Survey Completion - $0.50</li>
            <li>🎧 Transcription - $2.00</li>
        </ul>
        <a href="tasks.php" class="btn">View All Tasks</a>
    </section>

    <footer>
        <p>&copy; 2025 MicroTasker. All rights reserved.</p>
    </footer>
</body>
</html>
