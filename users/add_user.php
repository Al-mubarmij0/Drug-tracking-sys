<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Added user: $username (Role: $role)");
        header("Location: list.php?success=User+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+user");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
