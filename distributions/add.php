<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_check.php';
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stock_id = (int) $_POST['stock_id'];
    $recipient_id = (int) $_POST['recipient_id'];
    $quantity = (int) $_POST['quantity'];
    $date = $_POST['date_distributed'];

    // Validate stock availability
    $stockStmt = $conn->prepare("SELECT drug_name, quantity FROM stock WHERE id = ?");
    $stockStmt->bind_param("i", $stock_id);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    $stock = $stockResult->fetch_assoc();
    $stockStmt->close();

    if (!$stock) {
        header("Location: distribute.php?error=Stock+not+found");
        exit;
    }

    if ($quantity > $stock['quantity']) {
        header("Location: distribute.php?error=Quantity+exceeds+available+stock");
        exit;
    }

    // Insert distribution
    $stmt = $conn->prepare("INSERT INTO distributions (stock_id, recipient_id, quantity, date_distributed) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $stock_id, $recipient_id, $quantity, $date);

    if ($stmt->execute()) {
        // Deduct stock
        $updateStmt = $conn->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ?");
        $updateStmt->bind_param("ii", $quantity, $stock_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Log the action
        $drug = $stock['drug_name'];
        log_action($conn, $_SESSION['user_id'], "Distributed $quantity of $drug to recipient ID $recipient_id");

        header("Location: list.php?success=Distribution+added+successfully");
    } else {
        header("Location: distribute.php?error=Failed+to+save+distribution");
    }

    $stmt->close();
} else {
    header("Location: distribute.php");
    exit;
}
