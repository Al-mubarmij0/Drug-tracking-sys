<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Get username for logging
    $username = '';
    $fetch = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $fetch->bind_param("i", $id);
    $fetch->execute();
    $fetch->bind_result($username);
    $fetch->fetch();
    $fetch->close();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Deleted user: $username (ID: $id)");
        header("Location: list.php?success=User+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+user");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+user+ID");
}
