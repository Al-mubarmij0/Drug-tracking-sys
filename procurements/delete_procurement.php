<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch reference number for log
    $ref = '';
    $check = $conn->prepare("SELECT reference_no FROM procurements WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($ref);
    $check->fetch();
    $check->close();

    $stmt = $conn->prepare("DELETE FROM procurements WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Deleted procurement Ref#: $ref (ID: $id)");
        header("Location: list.php?success=Procurement+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+procurement");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+ID");
    exit;
}
