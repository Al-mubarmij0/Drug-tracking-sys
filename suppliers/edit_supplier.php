<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact_info']);

    $stmt = $conn->prepare("UPDATE suppliers SET name = ?, contact_info = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $contact, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated supplier: $name (ID: $id)");
        header("Location: list.php?success=Supplier+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+supplier");
    }
    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
