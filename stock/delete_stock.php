<?php
require_once __DIR__ . '/../config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM stock WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=Stock+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+stock");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+stock+ID");
}
