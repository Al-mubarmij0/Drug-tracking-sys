<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $stock_id = $_POST['stock_id'];
    $recipient_id = $_POST['recipient_id'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date_distributed'];

    $stmt = $conn->prepare("UPDATE distributions SET stock_id = ?, recipient_id = ?, quantity = ?, date_distributed = ? WHERE id = ?");
    $stmt->bind_param("iiisi", $stock_id, $recipient_id, $quantity, $date, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated distribution ID $id: stock ID $stock_id, recipient ID $recipient_id, quantity $quantity");
        header("Location: list.php?success=Distribution+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+distribution");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
