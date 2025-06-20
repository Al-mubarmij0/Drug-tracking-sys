<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $supplier_id = $_POST['supplier_id'];
    $date_procured = $_POST['date_procured'];
    $reference_no = trim($_POST['reference_no']);
    $notes = trim($_POST['notes']);

    $stmt = $conn->prepare("INSERT INTO procurements (supplier_id, date_procured, reference_no, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $supplier_id, $date_procured, $reference_no, $notes);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Added procurement Ref#: $reference_no for Supplier ID: $supplier_id");
        header("Location: list.php?success=Procurement+added+successfully");
    } else {
        header("Location: list.php?error=Failed+to+add+procurement");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
