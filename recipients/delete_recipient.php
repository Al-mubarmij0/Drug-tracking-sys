<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch recipient name for logging
    $name = '';
    $check = $conn->prepare("SELECT name FROM recipients WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($name);
    $check->fetch();
    $check->close();

    $stmt = $conn->prepare("DELETE FROM recipients WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Deleted recipient: $name (ID: $id)");
        header("Location: list.php?success=Recipient+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+recipient");
    }

    $stmt->close();
}
