<?php
require_once __DIR__ . '/../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=User+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+user");
    }
    $stmt->close();
} else {
    header("Location: list.php");
}
