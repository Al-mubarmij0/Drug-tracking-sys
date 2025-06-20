<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';
require_once __DIR__ . '/../helpers.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Get stock ID before deletion for logging
    $get = $conn->prepare("SELECT stock_id FROM distributions WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $getResult = $get->get_result();
    $data = $getResult->fetch_assoc();
    $get->close();

    $stmt = $conn->prepare("DELETE FROM distributions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Deleted distribution ID $id (Stock ID: {$data['stock_id']})");
        header("Location: list.php?success=Distribution+deleted+successfully");
    } else {
        header("Location: list.php?error=Failed+to+delete+distribution");
    }

    $stmt->close();
} else {
    header("Location: list.php?error=Invalid+ID");
    exit;
}
