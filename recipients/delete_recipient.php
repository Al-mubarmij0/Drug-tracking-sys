<?php
require_once __DIR__ . '/../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM recipients WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=Recipient+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+recipient");
    }
    $stmt->close();
}
