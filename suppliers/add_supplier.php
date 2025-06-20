<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact_info']);

    $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_info) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $contact);

    if ($stmt->execute()) {
        header("Location: list.php?success=Supplier+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+supplier");
    }
    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
