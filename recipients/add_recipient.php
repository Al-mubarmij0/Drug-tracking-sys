<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $identifier = trim($_POST['identifier']);

    $stmt = $conn->prepare("INSERT INTO recipients (name, identifier) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $identifier);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Added recipient: $name (ID: $identifier)");
        header("Location: list.php?success=Recipient+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+recipient");
    }
    $stmt->close();
}
