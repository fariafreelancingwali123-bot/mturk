<?php
session_start();
require 'db.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $passwordHash, $role);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed.";
        }
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MicroTasker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h1>Register</h1></header>
    <section style="text-align:center; padding:20px;">
        <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>

        <form method="post" action="register.php">
            <input type="text" name="name" placeholder="Your Name" required><br><br>
            <input type="email" name="email" placeholder="Email Address" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <select name="role" required>
                <option value="worker">Worker</option>
                <option value="requester">Requester</option>
            </select><br><br>
            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </section>
</body>
</html>
