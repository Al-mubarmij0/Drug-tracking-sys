<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $drug = trim($_POST['drug_name']);
    $batch = trim($_POST['batch_number']);
    $quantity = (int) $_POST['quantity'];
    $expiry = $_POST['expiry_date'];
    $procurement_id = !empty($_POST['procurement_id']) ? $_POST['procurement_id'] : null;

    $stmt = $conn->prepare("INSERT INTO stock (procurement_id, drug_name, batch_number, quantity, expiry_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $procurement_id, $drug, $batch, $quantity, $expiry);

    if ($stmt->execute()) {
        // ✅ Log the action
        log_action($conn, $_SESSION['user_id'], "Added stock: $drug (Qty: $quantity)");
        header("Location: list.php?success=Stock+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+stock");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
