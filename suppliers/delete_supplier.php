<?php
require_once __DIR__ . '/../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=Supplier+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+supplier");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=No+ID+provided");
}
