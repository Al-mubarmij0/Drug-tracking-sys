<?php
require_once __DIR__ . '/../config.php';
require_once '../session_check.php';
require_once '../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $supplier_id = $_POST['supplier_id'];
    $date_procured = $_POST['date_procured'];
    $reference_no = trim($_POST['reference_no']);
    $notes = trim($_POST['notes']);

    $stmt = $conn->prepare("UPDATE procurements SET supplier_id = ?, date_procured = ?, reference_no = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("isssi", $supplier_id, $date_procured, $reference_no, $notes, $id);

    if ($stmt->execute()) {
        log_action($conn, $_SESSION['user_id'], "Updated procurement Ref#: $reference_no (ID: $id)");
        header("Location: list.php?success=Procurement+updated+successfully");
    } else {
        header("Location: list.php?error=Failed+to+update+procurement");
    }

    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
