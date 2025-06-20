<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

// Handle POST request to update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];

    $updateQuery = "UPDATE users SET username=?, role=?";
    $params = [$username, $role];
    $types = "ss";

    // Optional password update
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery .= ", password=?";
        $params[] = $password;
        $types .= "s";
    }

    $updateQuery .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated user: $username (ID: $id, Role: $role)");
        header("Location: list.php?success=User+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+user");
    }

    $stmt->close();
    exit;
}
