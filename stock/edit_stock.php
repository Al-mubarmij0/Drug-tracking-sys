<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $drug = trim($_POST['drug_name']);
    $batch = trim($_POST['batch_number']);
    $quantity = (int) $_POST['quantity'];
    $expiry = $_POST['expiry_date'];
    $procurement_id = !empty($_POST['procurement_id']) ? $_POST['procurement_id'] : null;

    $stmt = $conn->prepare("UPDATE stock SET procurement_id=?, drug_name=?, batch_number=?, quantity=?, expiry_date=? WHERE id=?");
    $stmt->bind_param("issisi", $procurement_id, $drug, $batch, $quantity, $expiry, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated stock: $drug (ID: $id, Qty: $quantity)");
        header("Location: list.php?success=Stock+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+stock");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
