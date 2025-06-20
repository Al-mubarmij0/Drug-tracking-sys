<?php
require_once __DIR__ . '/../config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM procurements WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=Procurement+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+procurement");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+ID");
    exit;
}
