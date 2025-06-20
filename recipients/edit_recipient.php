<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $identifier = trim($_POST['identifier']);

    $stmt = $conn->prepare("UPDATE recipients SET name = ?, identifier = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $identifier, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated recipient: $name (ID: $id)");
        header("Location: list.php?success=Recipient+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+recipient");
    }
    $stmt->close();
}
