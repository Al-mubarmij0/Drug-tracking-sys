<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stock_id = $_POST['stock_id'];
    $recipient_id = $_POST['recipient_id'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date_distributed'];

    $stmt = $conn->prepare("INSERT INTO distributions (stock_id, recipient_id, quantity, date_distributed) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $stock_id, $recipient_id, $quantity, $date);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Added distribution: stock ID $stock_id to recipient ID $recipient_id (Qty: $quantity)");
        header("Location: list.php?success=Distribution+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+distribution");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
