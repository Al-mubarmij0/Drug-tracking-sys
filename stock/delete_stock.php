<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Get drug name for log before deletion
    $drug_name = '';
    $checkStmt = $conn->prepare("SELECT drug_name FROM stock WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($drug_name);
    $checkStmt->fetch();
    $checkStmt->close();

    $stmt = $conn->prepare("DELETE FROM stock WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Deleted stock: $drug_name (ID: $id)");
        header("Location: list.php?success=Stock+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+stock");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+stock+ID");
    exit;
}
